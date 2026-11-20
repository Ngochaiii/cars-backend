<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Jobs\ResolveLeadLocation;
use App\Models\ContactClick;
use App\Support\Url;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

/**
 * Nhận một lượt bấm nút gọi / Zalo — frontend.js gửi bằng sendBeacon ngay lúc
 * khách bấm, nên khách không phải chờ gì.
 *
 * Google Ads chỉ đếm "có lượt bấm". Ở đây ghi thêm nguồn quảng cáo, trang đã
 * xem (RememberVisit) và khu vực theo IP, để tư vấn viên biết khách bấm là
 * người quan tâm thật hay bấm thử.
 */
class ContactClickController extends Controller
{
    /** Bấm lại cùng nút trên cùng trang trong khoảng này thì coi là một lượt. */
    protected const DEDUPE_SECONDS = 30;

    public function __invoke(Request $request): Response
    {
        $data = $request->validate([
            'channel' => ['required', 'in:phone,zalo'],
            'page'    => ['nullable', 'string', 'max:255'],
        ]);

        $page = $this->path($data['page'] ?? null);
        $sessionHash = $this->visitorId($request);

        if ($sessionHash && $this->isRepeat($sessionHash, $data['channel'], $page)) {
            return response()->noContent();
        }

        $click = ContactClick::create([
            'channel'      => $data['channel'],
            'page'         => $page,
            'product_id'   => $this->productId($page),
            'source'       => $request->hasSession() ? $request->session()->get('visit.source') : null,
            'trail'        => $request->hasSession() ? $request->session()->get('visit.trail') : null,
            'device'       => $this->device((string) $request->userAgent()),
            'session_hash' => $sessionHash,
            'ip'           => $request->ip(),
        ]);

        // Tra khu vực SAU khi trả response, không qua hàng đợi: lượt bấm cần
        // có khu vực ngay cả khi worker hàng đợi không chạy.
        ResolveLeadLocation::dispatchAfterResponse($click);

        return response()->noContent();
    }

    /**
     * Mã ngẫu nhiên của lượt truy cập, giữ trong dữ liệu session (không dùng
     * session id vì id đổi khi Laravel regenerate). Gom các lượt bấm của
     * cùng một khách, và chặn bấm đúp.
     */
    protected function visitorId(Request $request): ?string
    {
        if (! $request->hasSession()) {
            return null;
        }

        if (! $request->session()->has('visit.id')) {
            $request->session()->put('visit.id', Str::random(32));
        }

        return $request->session()->get('visit.id');
    }

    /** Chỉ giữ đường dẫn: "/san-pham/vf-5?x=1" hay URL đầy đủ đều về "/san-pham/vf-5". */
    protected function path(?string $page): ?string
    {
        if (blank($page)) {
            return null;
        }

        $path = parse_url($page, PHP_URL_PATH);

        return is_string($path) && $path !== '' ? '/'.ltrim($path, '/') : null;
    }

    protected function isRepeat(string $sessionHash, string $channel, ?string $page): bool
    {
        return ContactClick::where('session_hash', $sessionHash)
            ->where('channel', $channel)
            ->where('page', $page)
            ->where('created_at', '>=', now()->subSeconds(self::DEDUPE_SECONDS))
            ->exists();
    }

    protected function productId(?string $page): ?int
    {
        $prefix = '/'.trim(Url::prefix('product'), '/').'/';

        if ($page === null || ! str_starts_with($page, $prefix)) {
            return null;
        }

        $slug = strtok(substr($page, strlen($prefix)), '/');

        return $slug ? catalog_model('product')::where('slug', $slug)->value('id') : null;
    }

    protected function device(string $userAgent): string
    {
        return preg_match('/Mobile|Android|iPhone|iPad/i', $userAgent) ? 'mobile' : 'desktop';
    }
}

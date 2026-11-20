<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Nhớ khách vào web từ đâu và đã xem những trang nào, để khi khách bấm nút
 * gọi / Zalo (ContactClickController) biết được lượt bấm đó thuộc lượt nhấp
 * quảng cáo nào và khách đã đi qua những trang gì.
 *
 * Chỉ ghi vào session của chính khách, không ghi DB ở mỗi lượt xem.
 */
class RememberVisit
{
    /** Số trang gần nhất giữ lại — đủ thấy hành trình, không phình session. */
    public const TRAIL_LIMIT = 15;

    public const SOURCE_KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gad_campaignid'];

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldTrack($request)) {
            $this->rememberSource($request);
            $this->rememberPage($request);
        }

        return $next($request);
    }

    protected function shouldTrack(Request $request): bool
    {
        return $request->isMethod('GET')
            && $request->hasSession()
            && ! $request->ajax()
            && ! $request->is('admin', 'admin/*', 'livewire/*', 'livewire-*', 'api/*', 'up');
    }

    /**
     * Nguồn tính theo lần vào ĐẦU TIÊN trong phiên: lượt nhấp quảng cáo là
     * cái tốn tiền, trang sau có utm khác (link nội bộ, Zalo OA) không được
     * đè lên.
     */
    protected function rememberSource(Request $request): void
    {
        if ($request->session()->has('visit.source')) {
            return;
        }

        $source = collect(self::SOURCE_KEYS)
            ->mapWithKeys(fn (string $key) => [$key => mb_substr((string) $request->query($key, ''), 0, 100)])
            ->filter()
            ->all();

        // Chỉ lưu CÓ hay KHÔNG có gclid — chuỗi gclid dài, không đọc được và
        // không cần cho việc tư vấn viên xem.
        if (filled($request->query('gclid')) || filled($request->query('gbraid')) || filled($request->query('wbraid'))) {
            $source['gclid'] = true;
        }

        if ($source === []) {
            return;
        }

        $request->session()->put('visit.source', $source + [
            'landing'   => '/'.ltrim($request->path(), '/'),
            'landed_at' => now()->format('d/m H:i'),
        ]);
    }

    protected function rememberPage(Request $request): void
    {
        $trail = $request->session()->get('visit.trail', []);
        $trail[] = ['p' => '/'.ltrim($request->path(), '/'), 't' => now()->format('H:i')];

        $request->session()->put('visit.trail', array_slice($trail, -self::TRAIL_LIMIT));
    }
}

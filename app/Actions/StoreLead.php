<?php

namespace App\Actions;

use App\Events\LeadReceived;
use App\Jobs\ResolveLeadLocation;
use App\Support\Phone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Nhận một lead. Dùng chung cho hai cửa vào:
 *   - API JSON  (POST /api/v1/leads)         — cho SPA/landing page ngoài
 *   - Form Blade (POST /gui-form/{form})     — trang khách xem, không cần JS
 *
 * Luật validate, honeypot và chống trùng nằm ở đây, không nằm ở controller,
 * để hai cửa vào không bao giờ lệch nhau.
 */
class StoreLead
{
    /**
     * @return ?Model  lead vừa tạo · lead trùng gần đây · null nếu là bot hoặc số/email bị chặn
     */
    public function handle(Request $request, Model $form): ?Model
    {
        // Bẫy bot: ô ẩn người thật để trống. Trả null, phía gọi vẫn báo thành
        // công để bot không dò được là đã bị chặn.
        if (filled($request->input(config('catalog.leads.honeypot', 'website')))) {
            return null;
        }

        // Đưa mọi ô kiểu "tel" về dạng 0xxxxxxxxx TRƯỚC khi validate, để
        // "098 765 4321" hay "+84987654321" đều qua rule VietnamPhone và lưu
        // cùng một dạng — dedupe theo số mới bắt được.
        foreach ($form->fields->where('type', 'tel') as $field) {
            if ($request->has($field->key)) {
                $request->merge([$field->key => Phone::normalize($request->input($field->key))]);
            }
        }

        // Luật validate dựng từ form_fields, không hardcode. ValidationException
        // tự trả 422 JSON cho API và redirect kèm lỗi cho form Blade.
        //
        // Bag đặt tên theo $form->key: khi trang có nhiều form dùng chung tên
        // trường (VD "name", "phone" ở cả "Đặt cọc" lẫn "Đăng ký lái thử"),
        // lỗi của form này không được tràn sang @error() của form khác. Chỉ
        // ảnh hưởng cách lỗi được gắn vào session cho Blade — JSON trả về
        // cho API vẫn y hệt vì response không phụ thuộc tên bag.
        $data = $request->validateWithBag(
            $form->key,
            $form->validationRules(),
            [],
            $form->validationAttributes(),
        );

        // Số/email trong Cài đặt → Chặn spam: im lặng bỏ qua như bot, để
        // người gửi không biết mình bị chặn mà đổi số khác.
        if ($this->isBlocked(Arr::get($data, 'phone'), Arr::get($data, 'email'))) {
            return null;
        }

        if ($existing = $this->recentDuplicate($form, Arr::get($data, 'phone'))) {
            return $existing;
        }

        $lead = $form->leads()->create([
            'data'       => $data,
            'name'       => Arr::get($data, 'name'),
            'phone'      => Arr::get($data, 'phone'),
            'email'      => Arr::get($data, 'email'),
            'product_id' => $request->integer('product_id') ?: null,
            'utm'        => $request->collect()->only([
                'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
            ])->filter()->all() ?: null,
            'referrer'   => $request->header('referer'),
            'ip'         => $request->ip(),
        ]);

        // Mail cho notify_emails + bắn webhook_url do listener lo (queued).
        LeadReceived::dispatch($lead);

        // Tra IP → tỉnh/thành chạy nền, không bắt khách chờ API ngoài.
        ResolveLeadLocation::dispatch($lead);

        return $lead;
    }

    protected function isBlocked(?string $phone, ?string $email): bool
    {
        $blocked = collect(preg_split('/[\r\n,;]+/', (string) catalog_setting('blocked_contacts')))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->map(fn (string $line) => str_contains($line, '@')
                ? mb_strtolower($line)
                : Phone::normalize($line));

        if ($blocked->isEmpty()) {
            return false;
        }

        return (filled($phone) && $blocked->contains(Phone::normalize($phone)))
            || (filled($email) && $blocked->contains(mb_strtolower(trim($email))));
    }

    /** Cùng form + cùng số trong cửa sổ ngắn thì trả lại lead cũ, không tạo mới. */
    protected function recentDuplicate(Model $form, ?string $phone): ?Model
    {
        $minutes = (int) config('catalog.leads.dedupe_minutes', 0);

        if ($minutes <= 0 || blank($phone)) {
            return null;
        }

        return $form->leads()
            ->where('phone', $phone)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->latest('id')
            ->first();
    }
}

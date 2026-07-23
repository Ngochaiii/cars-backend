<?php

namespace App\Http\Controllers\Api;

use App\Events\LeadReceived;
use App\Support\Catalog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class LeadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate(['form' => ['required', 'string']]);

        $form = Catalog::query('form')
            ->with('fields')
            ->where('key', $request->string('form'))
            ->where('is_active', true)
            ->firstOrFail();

        // Bẫy bot: ô ẩn người thật để trống. Trả 201 giả để bot không dò được.
        if (filled($request->input(config('catalog.leads.honeypot', 'website')))) {
            return $this->accepted($form, null);
        }

        // Luật validate dựng từ form_fields, không hardcode ở đây.
        $data = $request->validate(
            $form->validationRules(),
            [],
            $form->validationAttributes(),
        );

        // Chống trùng: cùng form + cùng số trong cửa sổ ngắn thì trả lại lead cũ.
        if ($existing = $this->recentDuplicate($form, Arr::get($data, 'phone'))) {
            return $this->accepted($form, $existing);
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

        return $this->accepted($form, $lead);
    }

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

    protected function accepted(Model $form, ?Model $lead): JsonResponse
    {
        return response()->json([
            'message' => $form->success_message ?: 'Đã nhận thông tin, chúng tôi sẽ liên hệ sớm.',
            'data'    => ['id' => $lead?->id],
        ], 201);
    }
}

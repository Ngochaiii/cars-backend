<?php

namespace App\Http\Controllers\Api;

use App\Events\LeadReceived;
use App\Support\Catalog;
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

        // Luật validate dựng từ form_fields, không hardcode ở đây.
        $data = $request->validate(
            $form->validationRules(),
            [],
            $form->validationAttributes(),
        );

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

        LeadReceived::dispatch($lead);

        return response()->json([
            'message' => $form->success_message ?: 'Đã nhận thông tin, chúng tôi sẽ liên hệ sớm.',
            'data'    => ['id' => $lead->id],
        ], 201);
    }
}

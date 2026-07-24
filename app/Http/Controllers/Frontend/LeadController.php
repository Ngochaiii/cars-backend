<?php

namespace App\Http\Controllers\Frontend;

use App\Actions\StoreLead;
use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Form trên trang Blade gửi thẳng vào đây — POST thường, không cần JS.
 * Cùng action với API nên honeypot, chống trùng, mail và webhook y hệt.
 */
class LeadController extends Controller
{
    public function __invoke(Request $request, Form $form, StoreLead $storeLead): RedirectResponse
    {
        abort_unless($form->is_active, 404);

        $form->load('fields');

        $storeLead->handle($request, $form);

        // Quay lại đúng trang có form, kèm câu cảm ơn do người nhập tự đặt.
        return back()
            ->with('lead_form_key', $form->key)
            ->with('lead_success', $form->success_message ?: 'Đã nhận thông tin, chúng tôi sẽ liên hệ sớm.');
    }
}

{{--
    Form lead dựng từ form_fields — thêm/bớt ô trong admin là frontend đổi theo,
    không sửa view. POST thường (không JS), lỗi validate và giá trị cũ giữ lại.

    Biến: $form (kèm fields) · $product (tuỳ chọn, gắn lead vào sản phẩm)
--}}
@php
    $honeypot = config('catalog.leads.honeypot', 'website');
    $sent     = session('lead_success') && session('lead_form_key') === $form->key;

    // Trang có thể có nhiều form dùng chung tên trường (VD "Đặt cọc" và
    // "Đăng ký lái thử" đều có "name"/"phone"). Lỗi validate đã tách theo
    // bag $form->key (xem StoreLead) — old() thì không có khái niệm bag,
    // nên chỉ đọc lại giá trị cũ khi CHÍNH form này vừa lỗi, không thì
    // form khác lỗi sẽ vô tình làm form này hiện lại giá trị không phải
    // của nó.
    $isThisForm = $errors->{$form->key}->any();
    $old = fn (string $key, mixed $default = null) => $isThisForm ? old($key, $default) : $default;
@endphp

<div class="lead-form-wrap" id="form-{{ $form->key }}">
    @if ($sent)
        <div class="lead-done">
            <div class="lead-done__mark" aria-hidden="true">&check;</div>
            <div class="lead-done__title">Đã nhận thông tin của bạn</div>
            <p>{{ session('lead_success') }}</p>
        </div>
    @endif

    <form class="lead-form" method="POST" action="{{ route('leads.store', $form) }}">
        @csrf

        @isset($product)
            <input type="hidden" name="product_id" value="{{ $product->id }}">
        @endisset

        {{-- Bẫy bot: ẩn bằng CSS, người thật không thấy nên luôn để trống. --}}
        <div class="honeypot" aria-hidden="true">
            <label for="{{ $honeypot }}">Bỏ trống ô này</label>
            <input type="text" id="{{ $honeypot }}" name="{{ $honeypot }}" tabindex="-1" autocomplete="off">
        </div>

        @foreach ($form->fields as $field)
            @continue($field->type === 'hidden')

            @php
                $required = in_array('required', (array) $field->rules, true);

                // Rule "max:300" vừa dùng để validate vừa đổ thành maxlength —
                // chặn ngay lúc gõ tử tế hơn là để người ta viết xong rồi báo lỗi.
                $maxLength = collect((array) $field->rules)
                    ->map(fn ($r) => is_string($r) && str_starts_with($r, 'max:') ? (int) substr($r, 4) : null)
                    ->filter()
                    ->first();
                $full     = $field->width !== 'half' || in_array($field->type, ['textarea', 'checkbox', 'radio'], true);
            @endphp

            @php
                // Checkbox chỉ một lựa chọn (ô đồng ý điều khoản): chính câu lựa
                // chọn đã là nhãn, in thêm nhãn field bên trên là lặp thừa.
                $soloCheckbox = $field->type === 'checkbox' && count((array) $field->options) === 1;
            @endphp

            <div class="field {{ $full ? 'field--full' : '' }} {{ in_array($field->type, ['radio', 'checkbox'], true) ? 'field--choice' : '' }}">
                @unless ($soloCheckbox)
                    <label for="f-{{ $form->key }}-{{ $field->key }}">
                        {!! catalog_field_label($field->label) !!}@if ($required) <span aria-hidden="true">*</span>@endif
                    </label>
                @endunless

                @switch($field->type)
                    @case('textarea')
                        <textarea id="f-{{ $form->key }}-{{ $field->key }}" name="{{ $field->key }}" rows="4"
                                  placeholder="{{ $field->placeholder }}"
                                  @if ($maxLength) maxlength="{{ $maxLength }}" @endif
                                  @if ($required) required @endif>{{ $old($field->key) }}</textarea>
                        @break

                    @case('select')
                        <select id="f-{{ $form->key }}-{{ $field->key }}" name="{{ $field->key }}"
                                @if ($required) required @endif>
                            <option value="">{{ $field->placeholder ?: '— Chọn —' }}</option>
                            @foreach ((array) $field->options as $value => $label)
                                <option value="{{ $value }}" @selected($old($field->key) == $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @break

                    @case('radio')
                    @case('checkbox')
                        @php $isCheckbox = $field->type === 'checkbox'; @endphp
                        <ul>
                            @foreach ((array) $field->options as $value => $label)
                                <li>
                                    <label>
                                        <input type="{{ $isCheckbox ? 'checkbox' : 'radio' }}"
                                               name="{{ $field->key }}{{ $isCheckbox ? '[]' : '' }}"
                                               value="{{ $value }}"
                                               @checked($isCheckbox
                                                   ? in_array($value, (array) $old($field->key, []), false)
                                                   : $old($field->key) == $value)>
                                        {{-- Bọc trong span: label là flex, để chữ trần
                                             thì mỗi thẻ <a> trong nhãn thành một
                                             phần tử flex riêng và câu bị xé làm đôi. --}}
                                        <span>{!! catalog_field_label($label) !!}</span>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                        @break

                    @default
                        <input type="{{ $field->type }}" id="f-{{ $form->key }}-{{ $field->key }}"
                               name="{{ $field->key }}" value="{{ $old($field->key) }}"
                               placeholder="{{ $field->placeholder }}"
                               @if ($maxLength) maxlength="{{ $maxLength }}" @endif
                               @if ($field->type === 'tel') inputmode="tel" @endif
                               @if ($required) required @endif>
                @endswitch

                @error($field->key, $form->key)
                    <p class="field__error">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        <div class="field field--full">
            <button class="btn btn--accent" type="submit">{{ $form->name }}</button>
        </div>
    </form>
</div>

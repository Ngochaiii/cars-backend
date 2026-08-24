{{--
    Một ô của form đặt cọc/lái thử.

    Khác partials/lead-form ở chỗ ô select được khai trong
    config('catalog.frontend.booking.card_fields') hiện thành lưới thẻ bấm
    (phương thức thanh toán, khung giờ, hình thức lái thử) đúng như bản
    thiết kế — vẫn là <input type=radio> nên gửi đi y hệt và tắt JS vẫn chọn
    được.

    Biến: $form · $field · $old (closure) · $asCards (closure)
--}}
@php
    $id       = 'bk-'.$form->key.'-'.$field->key;
    $required = in_array('required', (array) $field->rules, true);
    $full     = $field->width !== 'half' || in_array($field->type, ['textarea', 'checkbox', 'radio'], true);
    $cards    = $asCards($field);
@endphp

<div class="field {{ $full || $cards ? 'field--full' : '' }} {{ in_array($field->type, ['radio', 'checkbox'], true) ? 'field--choice' : '' }}">
    @if ($cards)
        <span class="field__label">{{ $field->label }}@if ($required) <span aria-hidden="true">*</span>@endif</span>

        <ul class="pick-grid pick-grid--wide">
            @foreach ((array) $field->options as $value => $label)
                @php $checked = $old($field->key) == $value; @endphp
                <li>
                    <label class="pick {{ $checked ? 'is-on' : '' }}">
                        <input type="radio" name="{{ $field->key }}" value="{{ $value }}"
                               @checked($checked) @if ($required) required @endif>
                        <b>{{ $label }}</b>
                    </label>
                </li>
            @endforeach
        </ul>
    @else
        <label for="{{ $id }}">{{ $field->label }}@if ($required) <span aria-hidden="true">*</span>@endif</label>

        @switch($field->type)
            @case('textarea')
                <textarea id="{{ $id }}" name="{{ $field->key }}" rows="3"
                          placeholder="{{ $field->placeholder }}"
                          @if ($required) required @endif>{{ $old($field->key) }}</textarea>
                @break

            @case('select')
                <select id="{{ $id }}" name="{{ $field->key }}" @if ($required) required @endif>
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
                                {{ $label }}
                            </label>
                        </li>
                    @endforeach
                </ul>
                @break

            @default
                <input type="{{ $field->type }}" id="{{ $id }}" name="{{ $field->key }}"
                       value="{{ $old($field->key) }}" placeholder="{{ $field->placeholder }}"
                       @if ($field->type === 'tel') inputmode="tel" @endif
                       @if ($required) required @endif>
        @endswitch
    @endif

    @error($field->key, $form->key)
        <p class="field__error">{{ $message }}</p>
    @enderror
</div>

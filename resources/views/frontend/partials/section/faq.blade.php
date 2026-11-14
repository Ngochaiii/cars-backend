{{-- Khối hỏi đáp. Câu hỏi và câu trả lời luôn hiện trên trang: Google chỉ chấp
     nhận structured data FAQPage khi người đọc thật sự thấy nội dung đó, và trợ
     lý AI trích dẫn được từng cặp hỏi–đáp đứng riêng.

     Tên mục ("Câu hỏi thường gặp") do .section__head dựng như các mục khác. --}}
@php
    $items = collect($section['items'] ?? [])
        ->filter(fn ($item) => is_array($item) && filled($item['question'] ?? null) && filled($item['answer'] ?? null));
@endphp

@if ($items->isNotEmpty())
    <div class="faq">
        @foreach ($items as $item)
            <div class="faq__item">
                <h3 class="faq__question">{{ $item['question'] }}</h3>
                <p class="faq__answer">{{ $item['answer'] }}</p>
            </div>
        @endforeach
    </div>
@endif

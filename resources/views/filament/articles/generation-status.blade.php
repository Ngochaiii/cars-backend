<div wire:poll.10s aria-live="polite" class="space-y-3">
    @if ($generation)
        <p><strong>{{ $generation->statusLabel() }}</strong> · {{ $generation->stageLabel() }} · #{{ $generation->id }}</p>
        @if ($generation->status === 'queued' && $generation->updated_at->lt(now()->subMinutes(5)))
            <p>Yêu cầu đang chờ lâu. Quản trị viên cần kiểm tra tiến trình xử lý bài viết trên máy chủ.</p>
        @endif
        @if ($generation->next_attempt_at)
            <p>Dự kiến thử lại: {{ $generation->next_attempt_at->timezone('Asia/Ho_Chi_Minh')->format('H:i:s d/m/Y') }}.</p>
        @endif
        @if ($generation->last_error)
            <p>{{ $generation->last_error }}</p>
        @endif
        @if ($generation->research)
            <details>
                <summary>Nghiên cứu, dàn ý và nguồn tham khảo</summary>
                <div class="space-y-3 mt-3">
                    <p>{{ $generation->research['brief']['intent_analysis'] ?? '' }}</p>
                    <ul class="list-disc pl-5">
                        @foreach ($generation->research['brief']['outline'] ?? [] as $heading)
                            <li>{{ $heading }}</li>
                        @endforeach
                    </ul>
                    <p>{{ $generation->research['brief']['evidence_notes'] ?? '' }}</p>
                    <p>Từ khóa liên quan: {{ implode(', ', $generation->research['brief']['related_keywords'] ?? []) }}</p>
                    @if ($generation->research['brief']['missing_information'] ?? [])
                        <p><strong>Dữ kiện cần bổ sung hoặc kiểm chứng:</strong></p>
                        <ul class="list-disc pl-5">
                            @foreach ($generation->research['brief']['missing_information'] as $missing)
                                <li>{{ $missing }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <p>{{ ($generation->research['grounded'] ?? false) ? 'Có nguồn từ Google Search; vẫn cần đối chiếu từng khẳng định.' : 'Chưa có nguồn tìm kiếm trực tiếp. Từ khóa là đề xuất ngữ nghĩa, không có số liệu lượng tìm kiếm.' }}</p>
                    @foreach ($generation->research['sources'] ?? [] as $source)
                        <p><a href="{{ $source['url'] }}" target="_blank" rel="noopener noreferrer" class="underline">{{ $source['title'] }}</a></p>
                    @endforeach
                    @if ($generation->research['search_entry_point'] ?? null)
                        <iframe title="Gợi ý tìm kiếm Google" sandbox="allow-popups allow-popups-to-escape-sandbox" referrerpolicy="no-referrer" srcdoc="{{ $generation->research['search_entry_point'] }}" style="width:100%;min-height:180px;border:0"></iframe>
                    @endif
                </div>
            </details>
        @endif
        @if ($generation->review)
            <p><strong>Cần biên tập trước khi đăng</strong></p>
            <ul class="list-disc pl-5">
                @foreach ($generation->review['warnings'] ?? [] as $warning)
                    <li>{{ $warning }}</li>
                @endforeach
            </ul>
            @foreach ($generation->review['similar_articles'] ?? [] as $similar)
                <p><a class="underline" href="{{ $similar['url'] }}" target="_blank" rel="noopener noreferrer">Bài cần so sánh: {{ $similar['title'] }}</a></p>
            @endforeach
            <p>{{ $generation->review['note'] }}</p>
        @endif
    @else
        <p>Chọn một yêu cầu đã lưu để xem tiến độ. Bạn có thể quay lại trang này sau.</p>
    @endif
</div>

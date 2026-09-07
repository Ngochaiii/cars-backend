<section class="tools home-section" data-home-section aria-labelledby="customer-tools-title">
    <div class="wrap tools__inner">
        <div class="tools__head" data-home-reveal>
            <div>
                <span class="eyebrow">{{ catalog_setting('tools_note', 'Hành trình sở hữu') }}</span>
                <h2 id="customer-tools-title">{{ catalog_setting('tools_title', 'Từ lựa chọn đầu tiên đến mỗi chuyến đi.') }}</h2>
            </div>
            <p>{{ catalog_setting('tools_text', 'Khám phá xe, dự toán chi phí và nhận hỗ trợ tại đại lý trong một hành trình liền mạch.') }}</p>
        </div>

        <div class="tools__grid">
            @foreach ($customerTools as $tool)
                <a class="tools__item {{ ($tool['feature'] ?? false) ? 'tools__item--feature' : '' }}"
                   href="{{ $tool['url'] }}" data-home-reveal>
                    @if (($tool['feature'] ?? false) && $toolsImage)
                        <span class="tools__media" data-home-parallax aria-hidden="true">
                            <x-img :src="$toolsImage" alt="" sizes="(max-width: 960px) 100vw, 50vw" />
                        </span>
                    @endif
                    <span class="tools__number" aria-hidden="true">{{ $tool['number'] }}</span>
                    <span class="tools__copy">
                        <span class="tools__name">{{ $tool['name'] }}</span>
                        <span class="tools__sub">{{ $tool['sub'] }}</span>
                    </span>
                    <span class="tools__arrow" aria-hidden="true">↗</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

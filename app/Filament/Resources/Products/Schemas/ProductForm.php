<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Forms\Components\NativeMediaUpload;
use App\Filament\Schemas\MoneyInput;
use App\Filament\Schemas\SectionsRepeater;
use App\Filament\Schemas\SeoSection;
use App\Filament\Schemas\SpecsRepeater;
use App\Support\Catalog;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        // Thứ tự các mục dưới đây TRÙNG thứ tự khối trên trang chi tiết. Người
        // nhập cuộn form từ trên xuống là thấy trang thành hình theo đúng mạch
        // đó — trước kia form xếp theo cấu trúc dữ liệu nên gõ xong không biết
        // chữ mình vừa nhập rơi vào chỗ nào ngoài frontend.
        return $schema->components([
            static::basics(),      // nhận diện + trạng thái (không nằm trên trang)
            static::hero(),        // 01 · hero
            static::intro(),       // 02 · khối mở đầu
            static::highlights(),  // 03 · dải chỉ số
            static::options(),     // 04 · bảng màu
            static::sections(),    // 05 · các mục nội dung
            static::specs(),       // 06 · thông số + ghi chú
            static::variants(),    // giá, giá gạch, số liệu cho bộ so sánh chi phí
            static::seo(),
        ]);
    }

    protected static function basics(): Section
    {
        return Section::make('Thông tin cơ bản')
            ->columns(2)
            ->schema([
                TextInput::make('name')
                    ->label('Tên '.Str::lower(Catalog::label('product.single')))
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (?string $state, callable $set) => $set('slug', Str::slug((string) $state))),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Đổi slug của bản đã publish thì nhớ tạo redirect 301.'),

                TextInput::make('tagline')
                    ->label('Tagline — tiêu đề lớn ở hero')
                    ->helperText('Đây là dòng chữ TO NHẤT trên trang, viết như một câu quảng cáo. Tên xe đã nằm ở dòng nhỏ phía trên rồi.')
                    ->columnSpanFull(),

                Select::make('category_id')
                    ->label('Danh mục')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                MoneyInput::make('price_from', 'Giá từ'),

                Select::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'draft' => 'Nháp',
                        'published' => 'Đã đăng',
                        'archived' => 'Lưu trữ',
                    ])
                    ->default('draft')
                    ->required()
                    ->selectablePlaceholder(false),

                DateTimePicker::make('published_at')
                    ->label('Đăng lúc')
                    ->seconds(false),
            ]);
    }

    /** 01 · Hero — khối đầu trang chiếm trọn màn hình. */
    protected static function hero(): Section
    {
        return Section::make('01 · Hero')
            ->description('Khối đầu trang: ảnh nền, tiêu đề lớn, đoạn dẫn, giá và hai nút.')
            ->columns(2)
            ->collapsible()
            ->schema([
                Select::make('hero.type')
                    ->label('Kiểu nền')
                    ->options(['image' => 'Ảnh', 'video' => 'Video'])
                    ->default('image')
                    ->live()
                    ->selectablePlaceholder(false),

                NativeMediaUpload::make('hero.src')
                    ->label('Ảnh desktop')
                    ->image()
                    ->directory('catalog/hero')
                    ->visible(fn (Get $get) => $get('hero.type') !== 'video'),

                NativeMediaUpload::make('hero.mobile_src')
                    ->label('Ảnh mobile')
                    ->helperText('Khuyến nghị 4:5 hoặc 3:4, chừa vùng an toàn cho tiêu đề và nút.')
                    ->image()
                    ->directory('catalog/hero')
                    ->visible(fn (Get $get) => $get('hero.type') !== 'video'),

                TextInput::make('hero.src')
                    ->label('Link video')
                    ->url()
                    ->visible(fn (Get $get) => $get('hero.type') === 'video'),

                NativeMediaUpload::make('hero.poster')
                    ->label('Ảnh poster')
                    ->image()
                    ->directory('catalog/hero')
                    ->visible(fn (Get $get) => $get('hero.type') === 'video'),

                Textarea::make('hero.lede')
                    ->label('Đoạn dẫn dưới tiêu đề')
                    ->helperText('Bỏ trống thì hero chỉ còn tiêu đề và giá — KHÔNG tự lấy mô tả SEO, vì mô tả đó đã dùng cho khối mở đầu bên dưới.')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    /** 02 · Khối mở đầu — đoạn căn giữa ngay dưới hero. */
    protected static function intro(): Section
    {
        return Section::make('02 · Khối mở đầu')
            ->description('Đoạn căn giữa ngay dưới hero, trước dải chỉ số.')
            ->collapsible()
            ->schema([
                TextInput::make('hero.intro_title')
                    ->label('Tiêu đề')
                    ->helperText('Đừng lặp lại tagline — trên trang hai câu này nằm cách nhau chưa tới một màn hình.')
                    ->columnSpanFull(),

                Textarea::make('hero.intro_body')
                    ->label('Nội dung')
                    ->helperText('Bỏ trống thì lấy tạm mô tả SEO.')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    protected static function highlights(): Section
    {
        return Section::make('03 · Dải chỉ số')
            ->description('Bốn ô số lớn ngay dưới khối mở đầu. Bản thiết kế dùng đúng 4 ô — thêm nữa thì lưới gãy.')
            ->visible(Catalog::feature('highlights'))
            ->collapsible()
            ->schema([
                Repeater::make('highlights')
                    ->hiddenLabel()
                    ->addActionLabel('+ Thêm')
                    ->defaultItems(0)
                    ->reorderableWithDragAndDrop()
                    ->itemLabel(fn (array $state): ?string => trim(($state['value'] ?? '').' '.($state['unit'] ?? '')) ?: null)
                    ->schema([
                        TextInput::make('value')->label('Giá trị')->required(),
                        TextInput::make('unit')->label('Đơn vị'),
                        TextInput::make('label')->label('Nhãn')->required(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }

    protected static function variants(): Section
    {
        return Section::make(Catalog::label('variant.plural'))
            ->description('Dùng để dựng thẻ phiên bản, lấy giá ở hero và số liệu cho các bộ tính chi phí.')
            ->visible(Catalog::feature('variants'))
            ->collapsible()
            ->schema([
                Repeater::make('variants')
                    ->hiddenLabel()
                    ->relationship()
                    ->defaultItems(0)
                    ->addActionLabel('+ Thêm '.Str::lower(Catalog::label('variant.single')))
                    ->orderColumn('sort')
                    ->reorderableWithDragAndDrop()
                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                    ->schema([
                        TextInput::make('name')->label('Tên')->required(),
                        MoneyInput::make('price', 'Giá'),
                        MoneyInput::make('price_original', 'Giá gạch'),
                        TextInput::make('note')->label('Ghi chú'),

                        /*
                         * Ảnh riêng của phiên bản. Khách phân biệt Eco với Plus
                         * bằng ảnh nhanh hơn đọc bảng số. Bỏ trống thì thẻ lùi
                         * về ảnh hero của xe, không để ô trống.
                         */
                        NativeMediaUpload::make('image')
                            ->label('Ảnh phiên bản')
                            ->helperText('Bỏ trống thì thẻ dùng ảnh chính của xe.')
                            ->image()
                            ->directory('catalog/variants')
                            ->imagePreviewHeight('120')
                            ->columnSpanFull(),

                        Toggle::make('is_default')
                            ->label('Phiên bản mặc định')
                            ->helperText('Phiên bản này được dùng trước cho giá, trả góp và so sánh chi phí.'),

                        TextInput::make('battery_kwh')
                            ->label('Dung lượng pin (kWh)')
                            ->numeric()
                            ->step(0.01)
                            ->visible(Catalog::feature('fuel_calc'))
                            ->helperText('Dùng cho bộ so sánh chi phí nhiên liệu. Xe xăng dầu bỏ trống.'),

                        TextInput::make('range_km')
                            ->label('Quãng đường mỗi lần sạc (km)')
                            ->numeric()
                            ->visible(Catalog::feature('fuel_calc')),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    protected static function options(): Section
    {
        return Section::make('04 · '.Catalog::label('option.plural'))
            ->description('Ảnh xe đổi màu theo dãy nút tròn. Khối này không có tiêu đề trên trang.')
            ->visible(Catalog::feature('options'))
            ->collapsible()
            ->schema([
                Repeater::make('options')
                    ->hiddenLabel()
                    ->relationship()
                    ->defaultItems(0)
                    ->addActionLabel('+ Thêm '.Str::lower(Catalog::label('option.single')))
                    ->orderColumn('sort')
                    ->reorderableWithDragAndDrop()
                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                    ->schema([
                        TextInput::make('name')->label('Tên')->required(),
                        ColorPicker::make('hex')->label('Mã màu'),
                        NativeMediaUpload::make('image')
                            ->label('Ảnh')
                            ->image()
                            ->directory('catalog/options'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }

    protected static function sections(): Section
    {
        return Section::make('05 · '.Catalog::label('sections'))
            ->description('Các khối nội dung giữa trang, hiện theo đúng thứ tự bạn sắp ở đây. Bố cục chọn ở từng mục. Nhãn/mô tả bỏ trống thì frontend không render.')
            ->collapsible()
            ->columnSpanFull()
            ->schema([
                SectionsRepeater::make(),
            ]);
    }

    protected static function specs(): Section
    {
        return Section::make('06 · '.Catalog::label('specs'))
            ->description('Lưới thông số phẳng, kèm hai ô ghi chú xếp cạnh nhau bên dưới.')
            ->visible(Catalog::feature('specs'))
            ->collapsible()
            ->collapsed()
            ->columnSpanFull()
            ->schema([
                SpecsRepeater::pasteField(),
                SpecsRepeater::make(),

                Repeater::make('spec_notes')
                    ->label('Ghi chú dưới bảng')
                    ->helperText('Hai ô chữ xếp cạnh nhau ngay dưới lưới thông số, VD "An toàn & an ninh" và "Hỗ trợ lái nâng cao ADAS". Bỏ trống thì không render.')
                    ->addActionLabel('+ Thêm ghi chú')
                    ->defaultItems(0)
                    ->reorderableWithDragAndDrop()
                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                    ->schema([
                        TextInput::make('label')->label('Tiêu đề')->required(),
                        Textarea::make('body')->label('Nội dung')->rows(3)->required(),
                    ])
                    ->columnSpanFull(),

                TextInput::make('brochure_url')
                    ->label('Link brochure riêng của xe')
                    ->helperText('Mỗi dòng xe dùng một brochure riêng. Bỏ trống thì lùi về link brochure chung trong Cài đặt.')
                    ->url()
                    ->columnSpanFull(),

                /*
                 * Hai cách khai thông số nữa, đặt CẠNH lưới ở trên chứ không
                 * thay thế: hãng thường phát hành bảng thông số dựng sẵn dạng
                 * ảnh hoặc PDF, gõ lại từng dòng vào lưới là việc thừa và dễ sai.
                 * Khai cái nào thì trang hiện cái đó; khai cả ba cũng được.
                 */
                NativeMediaUpload::make('spec_images')
                    ->label('Ảnh bảng thông số')
                    ->helperText('Bảng thông số dựng sẵn dạng ảnh. Kéo thả nhiều tấm nếu bảng dài phải cắt trang.')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->directory('catalog/specs')
                    ->imagePreviewHeight('120')
                    ->columnSpanFull(),

                NativeMediaUpload::make('spec_pdf')
                    ->label('Tài liệu PDF')
                    ->helperText('Catalogue hoặc bảng thông số bản PDF. Trang xe hiện nút tải về kèm dung lượng file.')
                    ->pdf()
                    ->directory('catalog/tai-lieu')
                    ->columnSpanFull(),

                TextInput::make('spec_pdf_label')
                    ->label('Chữ trên nút tải PDF')
                    ->placeholder('Tải thông số kỹ thuật (PDF)')
                    ->helperText('Bỏ trống thì dùng chữ mặc định.')
                    ->columnSpanFull(),
            ]);
    }

    protected static function seo(): Section
    {
        return SeoSection::make();
    }
}

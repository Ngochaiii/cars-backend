<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Schemas\MoneyInput;
use App\Filament\Schemas\SectionsRepeater;
use App\Filament\Schemas\SeoSection;
use App\Filament\Schemas\SpecsRepeater;
use App\Support\Catalog;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            static::basics(),
            static::highlights(),
            static::variants(),
            static::options(),
            static::sections(),
            static::specs(),
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
                    ->label('Tagline')
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
                        'draft'     => 'Nháp',
                        'published' => 'Đã đăng',
                        'archived'  => 'Lưu trữ',
                    ])
                    ->default('draft')
                    ->required()
                    ->selectablePlaceholder(false),

                DateTimePicker::make('published_at')
                    ->label('Đăng lúc')
                    ->seconds(false),

                Select::make('hero.type')
                    ->label('Hero')
                    ->options(['image' => 'Ảnh', 'video' => 'Video'])
                    ->default('image')
                    ->live()
                    ->selectablePlaceholder(false),

                FileUpload::make('hero.src')
                    ->label('Ảnh hero')
                    ->image()
                    ->directory('catalog/hero')
                    ->disk('public')
                    ->visible(fn (Get $get) => $get('hero.type') !== 'video'),

                TextInput::make('hero.src')
                    ->label('Link video')
                    ->url()
                    ->visible(fn (Get $get) => $get('hero.type') === 'video'),

                FileUpload::make('hero.poster')
                    ->label('Ảnh poster')
                    ->image()
                    ->directory('catalog/hero')
                    ->disk('public')
                    ->visible(fn (Get $get) => $get('hero.type') === 'video'),
            ]);
    }

    protected static function highlights(): Section
    {
        return Section::make('Chỉ số nổi bật')
            ->description('349 mã lực · 6.7 inch · 82 m² — tuỳ mặt hàng.')
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
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    protected static function options(): Section
    {
        return Section::make(Catalog::label('option.plural'))
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
                        FileUpload::make('image')
                            ->label('Ảnh')
                            ->image()
                            ->directory('catalog/options')
                            ->disk('public'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }

    protected static function sections(): Section
    {
        return Section::make(Catalog::label('sections'))
            ->description('Mỗi mục do bạn tự đặt tên. Nhãn/mô tả bỏ trống thì frontend không render.')
            ->collapsible()
            ->columnSpanFull()
            ->schema([
                SectionsRepeater::make(),
            ]);
    }

    protected static function specs(): Section
    {
        return Section::make(Catalog::label('specs'))
            ->visible(Catalog::feature('specs'))
            ->collapsible()
            ->collapsed()
            ->columnSpanFull()
            ->schema([
                SpecsRepeater::pasteField(),
                SpecsRepeater::make(),
            ]);
    }

    protected static function seo(): Section
    {
        return SeoSection::make();
    }
}

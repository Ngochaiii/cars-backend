<?php

namespace App\Filament\Resources\Banners;

use App\Filament\Concerns\HasCatalogNavigation;
use App\Filament\Resources\Banners\Pages\ManageBanners;
use App\Support\Catalog;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Banner hero trang chủ.
 *
 * Chưa khai banner nào thì trang chủ lùi về dùng ảnh của ba mặt hàng đầu —
 * xoá sạch bảng này không làm vỡ trang chủ.
 */
class BannerResource extends Resource
{
    use HasCatalogNavigation;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?int $navigationSort = 1;

    public static function getModel(): string
    {
        return Catalog::model('banner');
    }

    public static function getModelLabel(): string
    {
        return 'Banner';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Banner trang chủ';
    }

    public static function getNavigationGroup(): ?string
    {
        return config('catalog.admin.navigation_group');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Catalog::feature('banners');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('title')
                ->label('Tiêu đề')
                ->helperText('Dòng chữ to nhất trên banner.')
                ->required()
                ->columnSpanFull(),

            TextInput::make('eyebrow')
                ->label('Dòng nhỏ phía trên')
                ->helperText('Chữ hoa giãn cách, VD "Ưu đãi mùa hè · đến 31/08".'),

            Textarea::make('subtitle')
                ->label('Mô tả')
                ->rows(2)
                ->columnSpanFull(),

            FileUpload::make('image')
                ->label('Ảnh nền')
                ->image()
                ->directory('catalog/banners')
                ->disk('public')
                ->helperText('Bỏ trống thì banner dùng nền tối, chữ vẫn đọc được.')
                ->columnSpanFull(),

            TextInput::make('cta_label')->label('Nhãn nút'),

            TextInput::make('cta_url')
                ->label('Link nút')
                ->helperText('Nhãn không kèm link thì nút không hiện — tránh nút bấm không ra gì.'),

            Toggle::make('is_active')->label('Đang bật')->default(true),

            TextInput::make('sort')->label('Thứ tự')->numeric()->default(0),

            DateTimePicker::make('starts_at')
                ->label('Chạy từ')
                ->seconds(false)
                ->helperText('Bỏ trống = chạy ngay.'),

            DateTimePicker::make('ends_at')
                ->label('Chạy đến')
                ->seconds(false)
                ->helperText('Bỏ trống = không hết hạn.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                ImageColumn::make('image')->label('Ảnh')->disk('public'),
                TextColumn::make('title')->label('Tiêu đề')->searchable()->wrap(),
                IconColumn::make('is_active')->label('Bật')->boolean(),
                TextColumn::make('starts_at')->label('Từ')->dateTime('d/m/Y H:i')->placeholder('—'),
                TextColumn::make('ends_at')->label('Đến')->dateTime('d/m/Y H:i')->placeholder('—'),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageBanners::route('/')];
    }
}

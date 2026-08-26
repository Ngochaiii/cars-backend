<?php

namespace App\Filament\Resources\Provinces;

use App\Filament\Concerns\HasCatalogNavigation;
use App\Filament\Resources\Provinces\Pages\ManageProvinces;
use App\Support\Catalog;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Tỉnh / thành — chỉ để nhóm đại lý.
 *
 * Các cột phí lăn bánh của bảng này (registration_fee_rate, plate_fee,
 * inspection_fee, road_fee, insurance_fee) CỐ Ý không đưa vào form: bộ phận
 * khác quản lý, để lộ ra đây thì người nhập đại lý dễ sửa nhầm mà không biết
 * mình vừa đổi công thức tính lăn bánh của cả hệ thống.
 */
class ProvinceResource extends Resource
{
    use HasCatalogNavigation;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?int $navigationSort = 9;

    public static function getModel(): string
    {
        return Catalog::model('province');
    }

    public static function getModelLabel(): string
    {
        return 'Tỉnh / thành';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Tỉnh / thành';
    }

    public static function getNavigationGroup(): ?string
    {
        return config('catalog.admin.navigation_group');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Catalog::feature('dealers');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')->label('Tên tỉnh / thành')->required(),
            TextInput::make('code')->label('Mã')->helperText('VD 24 cho Bắc Giang. Không bắt buộc.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->label('Tên')->searchable(),
                TextColumn::make('code')->label('Mã')->placeholder('—'),
                TextColumn::make('dealers_count')->label('Số đại lý')->counts('dealers'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageProvinces::route('/')];
    }
}

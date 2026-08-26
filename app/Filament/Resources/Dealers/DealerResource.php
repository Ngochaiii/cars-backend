<?php

namespace App\Filament\Resources\Dealers;

use App\Filament\Concerns\HasCatalogNavigation;
use App\Filament\Resources\Dealers\Pages\ManageDealers;
use App\Support\Catalog;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DealerResource extends Resource
{
    use HasCatalogNavigation;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?int $navigationSort = 8;

    public static function getModel(): string
    {
        return Catalog::model('dealer');
    }

    public static function getModelLabel(): string
    {
        return 'Đại lý';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Đại lý';
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
            TextInput::make('name')->label('Tên đại lý')->required()->columnSpanFull(),

            Select::make('province_id')
                ->label('Tỉnh / thành')
                ->relationship('province', 'name')
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('phone')->label('Điện thoại')->tel(),

            Textarea::make('address')->label('Địa chỉ')->rows(2)->columnSpanFull(),

            TextInput::make('lat')
                ->label('Vĩ độ')
                ->numeric()
                ->helperText('Phải điền CẢ vĩ độ lẫn kinh độ thì trang đại lý mới hiện nút Chỉ đường — thiếu một cái là link mở ra giữa biển.'),

            TextInput::make('lng')->label('Kinh độ')->numeric(),

            Repeater::make('opening_hours')
                ->label('Giờ mở cửa')
                ->addActionLabel('+ Thêm dòng')
                ->defaultItems(0)
                ->simple(TextInput::make('line')->placeholder('T2–T7: 8:00–19:00'))
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->label('Tên')->searchable(),
                TextColumn::make('province.name')->label('Tỉnh / thành')->badge()->color('gray')->sortable(),
                TextColumn::make('address')->label('Địa chỉ')->wrap()->toggleable(),
                TextColumn::make('phone')->label('Điện thoại')->copyable()->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('province_id')
                    ->label('Tỉnh / thành')
                    ->relationship('province', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageDealers::route('/')];
    }
}

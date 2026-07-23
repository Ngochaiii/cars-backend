<?php

namespace App\Filament\Resources\Leads;

use BackedEnum;
use App\Filament\Concerns\HasCatalogNavigation;
use App\Filament\Resources\Leads\Pages\ManageLeads;
use App\Support\Catalog;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    use HasCatalogNavigation;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static ?int $navigationSort = 10;

    public static function getModel(): string
    {
        return Catalog::model('lead');
    }

    public static function getModelLabel(): string
    {
        return 'Liên hệ';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Liên hệ';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Khách hàng';
    }

    public static function getNavigationBadge(): ?string
    {
        $new = static::getModel()::where('status', 'new')->count();

        return $new ?: null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')->label('Họ tên'),
            TextInput::make('phone')->label('Điện thoại')->tel(),
            TextInput::make('email')->label('Email')->email(),

            Select::make('status')
                ->label('Trạng thái')
                ->options([
                    'new'       => 'Mới',
                    'contacted' => 'Đã liên hệ',
                    'done'      => 'Xong',
                    'spam'      => 'Spam',
                ])
                ->default('new')
                ->selectablePlaceholder(false),

            KeyValue::make('data')
                ->label('Dữ liệu gửi lên')
                ->disabled()
                ->columnSpanFull(),

            Textarea::make('note')->label('Ghi chú')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Lúc')->since()->sortable(),
                TextColumn::make('name')->label('Họ tên')->searchable(),
                TextColumn::make('phone')->label('Điện thoại')->searchable()->copyable(),
                TextColumn::make('form.name')->label('Form')->badge()->color('gray'),
                TextColumn::make('product.name')->label(Catalog::label('product.single'))->toggleable(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'contacted' => 'Đã liên hệ',
                        'done'      => 'Xong',
                        'spam'      => 'Spam',
                        default     => 'Mới',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'done'  => 'success',
                        'spam'  => 'danger',
                        'new'   => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'new'       => 'Mới',
                        'contacted' => 'Đã liên hệ',
                        'done'      => 'Xong',
                        'spam'      => 'Spam',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageLeads::route('/')];
    }
}

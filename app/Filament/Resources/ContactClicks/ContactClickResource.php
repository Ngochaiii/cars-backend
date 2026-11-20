<?php

namespace App\Filament\Resources\ContactClicks;

use App\Filament\Concerns\HasCatalogNavigation;
use App\Filament\Resources\ContactClicks\Pages\ManageContactClicks;
use App\Models\ContactClick;
use App\Support\Catalog;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Lượt bấm gọi / Zalo — chỉ xem. Dữ liệu do khách tạo ra khi bấm nút, admin
 * không thêm hay sửa tay.
 */
class ContactClickResource extends Resource
{
    use HasCatalogNavigation;

    protected static ?string $model = ContactClick::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    protected static ?int $navigationSort = 11;

    public static function getModelLabel(): string
    {
        return 'Lượt bấm gọi/Zalo';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Lượt bấm gọi/Zalo';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Khách hàng';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Lúc bấm')
                    ->dateTime('d/m H:i')
                    ->description(fn (ContactClick $record) => $record->created_at?->diffForHumans())
                    ->sortable(),
                TextColumn::make('channel')
                    ->label('Nút')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'zalo' ? 'Zalo' : 'Gọi')
                    ->color(fn (string $state) => $state === 'zalo' ? 'info' : 'success'),
                TextColumn::make('source')
                    ->label('Nguồn')
                    ->state(fn (ContactClick $record) => $record->sourceLabel())
                    ->description(fn (ContactClick $record) => self::sourceDetail($record))
                    ->badge()
                    ->color(fn (string $state) => $state === 'Google Ads' ? 'warning' : 'gray'),
                TextColumn::make('page')
                    ->label('Bấm ở trang')
                    ->description(fn (ContactClick $record) => $record->product?->name)
                    ->wrap(),
                TextColumn::make('trail')
                    ->label('Các trang đã xem')
                    ->state(fn (ContactClick $record) => $record->trailText() ?: '—')
                    ->description(fn (ContactClick $record) => count($record->trail ?? []).' trang')
                    ->wrap(),
                TextColumn::make('device')
                    ->label('Thiết bị')
                    ->formatStateUsing(fn (?string $state) => $state === 'mobile' ? 'Điện thoại' : 'Máy tính')
                    ->toggleable(),
                TextColumn::make('location')
                    ->label('Khu vực (theo IP)')
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('channel')
                    ->label('Nút')
                    ->options(['phone' => 'Gọi', 'zalo' => 'Zalo']),
                Filter::make('ads')
                    ->label('Chỉ khách từ Google Ads')
                    ->query(fn (Builder $query) => $query->where(fn (Builder $q) => $q
                        ->whereNotNull('source->gclid')
                        ->orWhereNotNull('source->gad_campaignid'))),
            ])
            ->recordActions([
                DeleteAction::make(),
            ]);
    }

    /** "Vào web 19/11 15:48 · /san-pham/vinfast-vf-5-plus · chiến dịch 24450242141" */
    protected static function sourceDetail(ContactClick $record): ?string
    {
        $source = $record->source;

        if (! $source) {
            return null;
        }

        return collect([
            isset($source['landed_at']) ? 'Vào web '.$source['landed_at'] : null,
            $source['landing'] ?? null,
            isset($source['gad_campaignid']) ? 'chiến dịch '.$source['gad_campaignid'] : ($source['utm_campaign'] ?? null),
        ])->filter()->implode(' · ');
    }

    public static function getPages(): array
    {
        return ['index' => ManageContactClicks::route('/')];
    }
}

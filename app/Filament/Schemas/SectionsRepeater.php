<?php

namespace App\Filament\Schemas;

use App\Support\Catalog;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Repeater cho cột `sections` — trái tim hệ thống.
 *
 * Mỗi mục là một khối do người nhập tự đặt tên. Không có danh sách block
 * cố định, không có field_definitions. Thêm mục = gõ tên, chọn layout, quăng ảnh.
 */
class SectionsRepeater
{
    public static function make(string $name = 'sections'): Repeater
    {
        return Repeater::make($name)
            ->hiddenLabel()
            ->addActionLabel('+ Thêm mục')
            ->defaultItems(0)
            ->reorderableWithDragAndDrop()
            ->collapsible()
            ->collapsed()
            ->cloneable()
            ->itemLabel(fn (array $state): string => static::itemLabel($state))
            ->schema([
                TextInput::make('title')
                    ->label('Tên mục')
                    ->required()
                    ->datalist(Catalog::sectionPresets())   // gợi ý, không ép chọn
                    ->columnSpan(2),

                Select::make('layout')
                    ->label('Bố cục')
                    ->options(Catalog::sectionLayouts())
                    ->default('cols-3')
                    ->selectablePlaceholder(false),

                Select::make('type')
                    ->label('Kiểu')
                    ->options(Catalog::sectionTypes())
                    ->default('media')
                    ->live()
                    ->selectablePlaceholder(false),

                Textarea::make('intro')
                    ->label('Đoạn mở đầu')
                    ->helperText('Bỏ trống thì frontend không render.')
                    ->rows(2)
                    ->columnSpanFull(),

                Textarea::make('body')
                    ->label('Nội dung')
                    ->rows(6)
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => $get('type') === 'text'),

                // Upload hàng loạt: kéo 12 ảnh vào một lần → 12 item.
                // Thiếu cái này thì mục Thư viện phải bấm 12 lần.
                FileUpload::make('bulk_upload')
                    ->label('Upload hàng loạt')
                    ->helperText('Kéo nhiều ảnh vào đây — mỗi ảnh thành một mục ảnh bên dưới.')
                    ->image()
                    ->multiple()
                    ->directory('catalog/sections')
                    ->disk('public')
                    ->dehydrated(false)
                    ->live()
                    ->afterStateUpdated(function (?array $state, Get $get, Set $set) {
                        if (blank($state)) {
                            return;
                        }

                        $items = $get('items') ?? [];

                        foreach ($state ?? [] as $file) {
                            if (! $file instanceof TemporaryUploadedFile) {
                                continue;
                            }

                            $items[(string) Str::uuid()] = [
                                // State của FileUpload là MẢNG đường dẫn, không phải chuỗi.
                                // Gán chuỗi ở đây thì đến lúc validate sẽ nổ TypeError.
                                'image' => [$file->store('catalog/sections', 'public')],
                                'label' => '',
                                'desc'  => '',
                            ];
                        }

                        $set('items', $items);
                        $set('bulk_upload', []);
                    })
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => in_array($get('type'), ['media', null], true)),

                Repeater::make('items')
                    ->label('Ảnh trong mục')
                    ->addActionLabel('+ Thêm ảnh')
                    ->defaultItems(0)
                    ->reorderableWithDragAndDrop()
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                    ->grid(2)
                    ->schema([
                        FileUpload::make('image')
                            ->label('Ảnh')
                            ->image()
                            ->directory('catalog/sections')
                            ->disk('public')
                            ->imagePreviewHeight('120'),

                        TextInput::make('label')
                            ->label('Nhãn')
                            ->helperText('Bỏ trống thì không render.'),

                        Textarea::make('desc')
                            ->label('Mô tả')
                            ->rows(2),
                    ])
                    ->columnSpanFull()
                    ->visible(fn (Get $get) => in_array($get('type'), ['media', 'video', null], true)),
            ])
            ->columns(4)
            ->columnSpanFull();
    }

    protected static function itemLabel(array $state): string
    {
        $title = $state['title'] ?? 'Mục chưa đặt tên';
        $layout = Catalog::sectionLayouts()[$state['layout'] ?? ''] ?? null;
        $count = count($state['items'] ?? []);

        return trim(implode('  ·  ', array_filter([
            $title,
            $layout,
            $count ? "{$count} ảnh" : null,
        ])));
    }
}

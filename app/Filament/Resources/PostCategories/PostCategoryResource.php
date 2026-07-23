<?php

namespace App\Filament\Resources\PostCategories;

use BackedEnum;
use App\Filament\Concerns\HasCatalogNavigation;
use App\Filament\Resources\PostCategories\Pages\ManagePostCategories;
use App\Support\Catalog;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostCategoryResource extends Resource
{
    use HasCatalogNavigation;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static ?int $navigationSort = 4;

    public static function getModel(): string
    {
        return Catalog::model('post_category');
    }

    public static function getModelLabel(): string
    {
        return 'Chuyên mục';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Chuyên mục';
    }

    public static function getNavigationGroup(): ?string
    {
        return config('catalog.admin.navigation_group');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')
                ->label('Tên')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn (?string $state, callable $set) => $set('slug', Str::slug((string) $state))),

            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->unique(ignoreRecord: true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('name')->label('Tên')->searchable(),
                TextColumn::make('posts_count')->label('Số bài')->counts('posts'),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManagePostCategories::route('/')];
    }
}

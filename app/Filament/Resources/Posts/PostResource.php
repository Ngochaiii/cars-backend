<?php

namespace App\Filament\Resources\Posts;

use BackedEnum;
use App\Filament\Concerns\HasCatalogNavigation;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Schemas\SectionsRepeater;
use App\Filament\Schemas\SeoSection;
use App\Support\Catalog;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    use HasCatalogNavigation;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?int $navigationSort = 3;

    public static function getModel(): string
    {
        return Catalog::model('post');
    }

    public static function getModelLabel(): string
    {
        return 'Bài viết';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Bài viết';
    }

    public static function getNavigationGroup(): ?string
    {
        return config('catalog.admin.navigation_group');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thông tin bài viết')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Tiêu đề')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (?string $state, callable $set) => $set('slug', Str::slug((string) $state))),

                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Select::make('post_category_id')
                        ->label('Chuyên mục')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload(),

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

                    FileUpload::make('cover')
                        ->label('Ảnh bìa')
                        ->image()
                        ->directory('catalog/posts')
                        ->disk('public'),

                    Textarea::make('excerpt')
                        ->label('Tóm tắt')
                        ->rows(3)
                        ->maxLength(400)
                        ->columnSpanFull(),
                ]),

            Section::make('Nội dung')
                ->description('Cùng cơ chế mục như sản phẩm — thêm mục ảnh, mục văn bản, mục video tuỳ bài.')
                ->collapsible()
                ->columnSpanFull()
                ->schema([SectionsRepeater::make()]),

            SeoSection::make(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('cover')->label('')->disk('public')->imageHeight(40),

                TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('category.name')->label('Chuyên mục')->badge()->toggleable(),

                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'published' => 'Đã đăng',
                        'archived'  => 'Lưu trữ',
                        default     => 'Nháp',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'archived'  => 'gray',
                        default     => 'warning',
                    }),

                TextColumn::make('published_at')->label('Đăng lúc')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'draft'     => 'Nháp',
                        'published' => 'Đã đăng',
                        'archived'  => 'Lưu trữ',
                    ]),

                SelectFilter::make('category')
                    ->label('Chuyên mục')
                    ->relationship('category', 'name')
                    ->preload(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit'   => EditPost::route('/{record}/edit'),
        ];
    }
}

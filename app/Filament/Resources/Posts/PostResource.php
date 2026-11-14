<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Concerns\HasCatalogNavigation;
use App\Filament\Forms\Components\NativeMediaUpload;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Schemas\ArticleGenerationSection;
use App\Filament\Schemas\SeoSection;
use App\Support\Catalog;
use App\Support\Url;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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

                    NativeMediaUpload::make('cover')
                        ->label('Ảnh bìa')
                        ->image()
                        ->live()
                        ->directory('catalog/posts'),

                    Select::make('cover_width')
                        ->label('Bề rộng ảnh bìa')
                        ->options([
                            'narrow' => 'Cột chữ (thẳng hàng với bài)',
                            'wide' => 'Rộng bằng khung nội dung',
                            'full' => 'Tràn hết màn hình',
                        ])
                        ->live()
                        ->placeholder('Cột chữ')
                        ->helperText(fn (Get $get) => match ($get('cover_width')) {
                            'wide' => 'Khung ảnh 16:7 rộng 1456 px — khuyến nghị 2000 × 875 px.',
                            'full' => 'Khung ảnh 16:7 tràn hết màn hình — khuyến nghị 2560 × 1120 px.',
                            default => 'Ảnh bìa thẳng hàng với chữ, khung 16:7 rộng khoảng 1014 px — khuyến nghị 1600 × 700 px.',
                        }),

                    Textarea::make('excerpt')
                        ->label('Tóm tắt')
                        ->rows(3)
                        ->maxLength(400)
                        ->columnSpanFull(),
                ]),

            ArticleGenerationSection::make(),

            Section::make('Nội dung')
                ->description('Dán nội dung từ website, Word hoặc Google Docs. Hệ thống giữ tiêu đề, đoạn văn, chữ đậm, danh sách và liên kết.')
                ->collapsible()
                ->columnSpanFull()
                ->schema([
                    Textarea::make('seo.keywords')
                        ->label('Từ khóa SEO Gemini đã dùng')
                        ->helperText('Danh sách để biên tập viên kiểm tra. Website không tạo thẻ meta keywords và không nhồi từ khóa máy móc.')
                        ->rows(2)
                        ->columnSpanFull(),

                    RichEditor::make('article_body')
                        ->label('Nội dung bài viết')
                        ->helperText('Chỉ cần dán và chỉnh lại nội dung tại đây; không cần tạo tên mục hay chọn bố cục.')
                        ->toolbarButtons([
                            ['bold', 'italic', 'underline', 'strike', 'link'],
                            ['h2', 'h3', 'bulletList', 'orderedList', 'blockquote', 'table'],
                            ['undo', 'redo'],
                        ])
                        ->columnSpanFull(),
                ]),

            Hidden::make('ai_generation_id'),
            Checkbox::make('ai_reviewed')
                ->label('Tôi đã đọc duyệt bài AI, đối chiếu dữ kiện và nguồn trước khi đăng')
                ->visible(fn (Get $get): bool => filled($get('ai_generation_id'))),
            SeoSection::make(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                ImageColumn::make('cover')
                    ->label('')
                    ->state(fn ($record): ?string => Url::asset($record->cover))
                    ->imageHeight(40),

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
                        'archived' => 'Lưu trữ',
                        default => 'Nháp',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'archived' => 'gray',
                        default => 'warning',
                    }),

                TextColumn::make('published_at')->label('Đăng lúc')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'draft' => 'Nháp',
                        'published' => 'Đã đăng',
                        'archived' => 'Lưu trữ',
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
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}

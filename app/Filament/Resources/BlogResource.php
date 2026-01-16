<?php


namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Blog Yönetimi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(12)
                    ->schema([
                        // SOL KOLON (4) - Görsel ve Meta Veriler
                        Forms\Components\Section::make('Yayın Ayarları')
                            ->columnSpan(4)
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Kapak Görseli')
                                    ->disk('uploads')
                                    ->directory('blogs')
                                    ->image()
                                    ->helperText('Blog yazısı için ana görsel.'),

                                Forms\Components\Select::make('blog_category_id')
                                    ->label('Kategori')
                                    ->relationship('category', 'title') // Modeldeki ilişki üzerinden
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('Bu yazının hangi kategoriye ait olduğunu seçin.'),

                                Forms\Components\TagsInput::make('tags')
                                    ->label('Etiketler')
                                    ->placeholder('Yeni etiket...')
                                    ->helperText('Yazı ile ilgili anahtar kelimeler girin.'),

                                Forms\Components\Toggle::make('is_published')
                                    ->label('Yayınla')
                                    ->default(true)
                                    ->helperText('Bu yazı web sitesinde görünsün mü?'),
                            ]),

                        // SAĞ KOLON (8) - İçerik
                        Forms\Components\Section::make('Blog İçeriği')
                            ->columnSpan(8)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Yazı Başlığı')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                                        $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                                    ->helperText('Okuyucuların göreceği ana başlık.'),

                                Forms\Components\TextInput::make('slug')
                                    ->label('URL (Slug)')
                                    ->required()
                                    ->unique(Blog::class, 'slug', ignoreRecord: true)
                                    ->helperText('Tarayıcı adres çubuğundaki link yapısı.'),

                                Forms\Components\RichEditor::make('desc')
                                    ->label('İçerik')
                                    ->required()
                                    ->fileAttachmentsDisk('uploads')
                                    ->fileAttachmentsDirectory('blog-content')
                                    ->columnSpanFull()
                                    ->helperText('Blog yazısının tam metni.'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->disk('uploads'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(30),
                Tables\Columns\TextColumn::make('category.title')
                    ->label('Kategori')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')->boolean()->label('Durum'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('blog_category_id')
                    ->relationship('category', 'title')
                    ->label('Kategoriye Göre Filtrele'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogCategoryResource\Pages;
use App\Models\BlogCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogCategoryResource extends Resource
{
    protected static ?string $model = BlogCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Blog Yönetimi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(12)
                    ->schema([
                        // SOL KOLON (4)
                        Forms\Components\Section::make('Kategori Görseli')
                            ->columnSpan(4)
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Kategori İkonu/Görseli')
                                    ->disk('uploads')
                                    ->directory('categories')
                                    ->image()
                                    ->helperText('Kategoriyi temsil eden görseli yükleyin.'),

                                Forms\Components\Toggle::make('is_published')
                                    ->label('Yayınla')
                                    ->default(true)
                                    ->helperText('Kategori sitede aktif görünsün mü?'),
                            ]),

                        // SAĞ KOLON (8)
                        Forms\Components\Section::make('Kategori Detayları')
                            ->columnSpan(8)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Kategori Adı')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                                        $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                                    ->helperText('Örn: Teknoloji, Yaşam, Güncel Haberler'),

                                Forms\Components\TextInput::make('slug')
                                    ->label('URL Uzantısı (Slug)')
                                    ->required()
                                    ->unique(BlogCategory::class, 'slug', ignoreRecord: true)
                                    ->helperText('Tarayıcı çubuğunda görünecek adres (örn: teknoloji-haberleri).'),

                                Forms\Components\RichEditor::make('desc')
                                    ->label('Kısa Açıklama')
                                    ->helperText('Bu kategori hakkında kısa bir ön bilgi yazın.')
                                    ->fileAttachmentsDisk('uploads')
                                    ->fileAttachmentsDirectory('category-content')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->disk('uploads'),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\IconColumn::make('is_published')->boolean()->label('Durum'),
            ])
            ->filters([])
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
            'index' => Pages\ListBlogCategories::route('/'),
            'create' => Pages\CreateBlogCategory::route('/create'),
            'edit' => Pages\EditBlogCategory::route('/{record}/edit'),
        ];
    }
}
<?php


namespace App\Filament\Resources;

use App\Filament\Resources\MenuCategoryResource\Pages;
use App\Models\MenuCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuCategoryResource extends Resource
{
    protected static ?string $model = MenuCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'Menü Yönetimi';
    protected static ?string $modelLabel = 'Menü Kategorisi';

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
                                    ->label('Görsel')
                                    ->disk('uploads')
                                    ->directory('menu-categories')
                                    ->image()
                                    ->helperText('Kategoriyi temsil eden bir görsel veya ikon yükleyin.'),

                                Forms\Components\Toggle::make('is_published')
                                    ->label('Yayınla')
                                    ->default(true)
                                    ->helperText('Bu kategori menüde aktif olarak görünsün mü?'),
                            ]),

                        // SAĞ KOLON (8)
                        Forms\Components\Section::make('Kategori Bilgileri')
                            ->columnSpan(8)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Kategori Adı')
                                    ->required()
                                    ->helperText('Örn: Sıcak İçecekler, Burgerler, Tatlılar'),

                                Forms\Components\RichEditor::make('desc')
                                    ->label('Açıklama')
                                    ->helperText('Kategori hakkında kısa bir bilgi metni yazın.')
                                    ->fileAttachmentsDisk('uploads')
                                    ->fileAttachmentsDirectory('menu-category-content')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order') // Sürükle-bırak sıralama aktif
            ->defaultSort('order', 'asc')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->disk('uploads'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('order')->label('Sıralama'),
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
            'index' => Pages\ListMenuCategories::route('/'),
            'create' => Pages\CreateMenuCategory::route('/create'),
            'edit' => Pages\EditMenuCategory::route('/{record}/edit'),
        ];
    }
}
<?php


namespace App\Filament\Resources;

use App\Filament\Resources\MenuItemResource\Pages;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $navigationGroup = 'Menü Yönetimi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(12)
                    ->schema([
                        // SOL KOLON (4)
                        Forms\Components\Section::make('Ürün Görseli ve Durum')
                            ->columnSpan(4)
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Ürün Fotoğrafı')
                                    ->disk('uploads')
                                    ->directory('menu-items')
                                    ->image()
                                    ->helperText('Ürünün iştah açıcı bir fotoğrafını yükleyin.'),

                                Forms\Components\Select::make('menu_category_id')
                                    ->label('Kategori')
                                    ->relationship('category', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('Bu ürünün hangi menü grubunda olduğunu seçin.'),

                                Forms\Components\Toggle::make('is_published')
                                    ->label('Yayınla')
                                    ->default(true),
                            ]),

                        // SAĞ KOLON (8)
                        Forms\Components\Section::make('Ürün Detayları')
                            ->columnSpan(8)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Ürün Adı')
                                    ->required()
                                    ->helperText('Örn: Adana Kebap, Sufle, Espresso'),

                                Forms\Components\TextInput::make('price')
                                    ->label('Fiyat')
                                    ->numeric()
                                    ->prefix('₺')
                                    ->helperText('Ürün satış fiyatı.'),

                                Forms\Components\RichEditor::make('desc')
                                    ->label('İçerik/Açıklama')
                                    ->helperText('Ürün içeriği, gramaj veya alerjen bilgileri.')
                                    ->fileAttachmentsDisk('uploads')
                                    ->fileAttachmentsDirectory('menu-item-content')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->disk('uploads'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.title')->label('Kategori'),
                Tables\Columns\TextColumn::make('price')->money('TRY')->label('Fiyat'),
                Tables\Columns\IconColumn::make('is_published')->boolean()->label('Durum'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('menu_category_id')
                    ->relationship('category', 'title')
                    ->label('Kategoriye Göre'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuItemResource\Pages;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

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
                        // SOL KOLON
                        Forms\Components\Section::make('Ürün Görseli ve Durum')
                            ->columnSpan(4)
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Ürün Fotoğrafı')
                                    ->disk('uploads')
                                    ->directory('menu-items')
                                    ->image(),

                                Forms\Components\Select::make('menu_category_id')
                                    ->label('Kategori')
                                    ->relationship('category', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\Toggle::make('is_published')
                                    ->label('Yayınla')
                                    ->default(true),
                            ]),

                        // SAĞ KOLON
                        Forms\Components\Section::make('Ürün Detayları')
                            ->columnSpan(8)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Ürün Adı')
                                    ->required(),

                                Forms\Components\TextInput::make('price')
                                    ->label('Fiyat')
                                    ->numeric()
                                    ->prefix('₺'),

                                Forms\Components\RichEditor::make('desc')
                                    ->label('İçerik/Açıklama')
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
                ImageColumn::make('image')
                    ->disk('uploads')
                    ->circular(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->wrap(), // Uzun isimleri alt satıra kırar

                TextColumn::make('category.title')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray'),

                // DARALTILMIŞ FİYAT INPUTU
                TextInputColumn::make('price')
                    ->label('Fiyat (₺)')
                    ->type('number')
                    ->width('100px') // Kutucuğu daralttık
                    ->alignCenter(),

                // HIZLI YAYINLAMA BUTONU (AÇ-KAPA)
                ToggleColumn::make('is_published')
                    ->label('Yayınla')
                    ->alignCenter(),
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
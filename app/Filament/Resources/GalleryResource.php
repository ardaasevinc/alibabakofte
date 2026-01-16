<?php


namespace App\Filament\Resources;

use App\Filament\Resources\GalleryResource\Pages;
use App\Models\Gallery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(12)
                    ->schema([
                        // SOL KOLON (4)
                        Forms\Components\Section::make('Görsel')
                            ->description('Galeri öğesinin görselini ve durumunu belirleyin.')
                            ->columnSpan(4)
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Fotoğraf')
                                    ->disk('uploads')
                                    ->directory('gallery')
                                    ->image()
                                    ->required()
                                    ->helperText('Galeri için yüksek kaliteli bir görsel seçin.'),

                                Forms\Components\Toggle::make('is_published')
                                    ->label('Yayınla')
                                    ->helperText('Bu fotoğraf galeride görünsün mü?')
                                    ->default(true),
                            ]),

                        // SAĞ KOLON (8)
                        Forms\Components\Section::make('İçerik')
                            ->description('Fotoğraf ile ilgili başlık ve açıklama.')
                            ->columnSpan(8)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Başlık')
                                    ->required()
                                    ->helperText('Görsel için kısa bir başlık.'),

                                Forms\Components\RichEditor::make('desc')
                                    ->label('Açıklama')
                                    ->helperText('Görsel hakkında detaylı bilgi veya alt yazı.')
                                    ->fileAttachmentsDisk('uploads')
                                    ->fileAttachmentsDirectory('gallery-content')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Sıralama özelliğini aktif eden anahtar metod:
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->disk('uploads'),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('order')->label('Sıra'),
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
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }
}
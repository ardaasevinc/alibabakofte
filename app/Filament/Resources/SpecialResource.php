<?php


namespace App\Filament\Resources;

use App\Filament\Resources\SpecialResource\Pages;
use App\Models\Special;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SpecialResource extends Resource
{
    protected static ?string $model = Special::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(12)
                    ->schema([
                        // SOL KOLON (4 Sütun)
                        Forms\Components\Section::make('Görsel Bilgisi')
                            ->description('Öne çıkan görsel ve görünürlük ayarları.')
                            ->columnSpan(4)
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('İkon veya Görsel')
                                    ->disk('uploads')
                                    ->directory('specials')
                                    ->image()
                                    ->helperText('Bu özellik için bir ikon veya tanıtıcı görsel seçin.'),

                                Forms\Components\Toggle::make('is_published')
                                    ->label('Yayınla')
                                    ->helperText('Bu özel içerik yayına alınsın mı?')
                                    ->default(true),
                            ]),

                        // SAĞ KOLON (8 Sütun)
                        Forms\Components\Section::make('İçerik Detayları')
                            ->description('Başlık ve açıklama bilgileri.')
                            ->columnSpan(8)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Özellik Başlığı')
                                    ->required()
                                    ->helperText('Kısa ve dikkat çekici bir başlık yazın.'),

                                Forms\Components\RichEditor::make('desc')
                                    ->label('Detaylı Açıklama')
                                    ->helperText('Özelliğin detaylarını burada açıklayın.')
                                    ->fileAttachmentsDisk('uploads')
                                    ->fileAttachmentsDirectory('special-content')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order') // Sıralama özelliği aktif
            ->defaultSort('order', 'asc')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->disk('uploads'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('order')->label('Sıralama'),
                Tables\Columns\IconColumn::make('is_published')->boolean()->label('Aktif'),
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
            'index' => Pages\ListSpecials::route('/'),
            'create' => Pages\CreateSpecial::route('/create'),
            'edit' => Pages\EditSpecial::route('/{record}/edit'),
        ];
    }
}
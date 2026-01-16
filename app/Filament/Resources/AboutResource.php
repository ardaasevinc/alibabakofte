<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutResource\Pages;
use App\Models\About;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AboutResource extends Resource
{
    protected static ?string $model = About::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    // Singleton Kuralı: 1 kayıttan fazla oluşturulmasını engeller
    public static function canCreate(): bool
    {
        return About::count() < 1;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(12)
                    ->schema([
                        // SOL KOLON (4 Sütun)
                        Forms\Components\Section::make('Görsel ve Durum')
                            ->description('Medya dosyaları ve yayın ayarları.')
                            ->columnSpan(4)
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Hakkımızda Görseli')
                                    ->disk('uploads') // uploads diski kullanılıyor
                                    ->directory('about')
                                    ->image()
                                    ->helperText('Sayfanın solunda veya üstünde görünecek ana görsel.'),

                                Forms\Components\Toggle::make('is_published')
                                    ->label('Yayınla')
                                    ->helperText('İçeriğin web sitesinde görünürlüğünü kontrol eder.')
                                    ->default(true),
                            ]),

                        // SAĞ KOLON (8 Sütun)
                        Forms\Components\Section::make('İçerik Bilgileri')
                            ->description('Başlık ve detaylı açıklama alanları.')
                            ->columnSpan(8)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Sayfa Başlığı')
                                    ->required()
                                    ->helperText('Hakkımızda bölümünün ana başlığı.'),

                                Forms\Components\RichEditor::make('desc')
                                    ->label('Açıklama Metni')
                                    ->helperText('Şirketiniz veya projeniz hakkında detaylı bilgi girin.')
                                    ->fileAttachmentsDisk('uploads') // Editör içindeki yüklemeler için
                                    ->fileAttachmentsDirectory('about-content')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('uploads'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Yayın Durumu')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbouts::route('/'),
            'create' => Pages\CreateAbout::route('/create'),
            'edit' => Pages\EditAbout::route('/{record}/edit'),
        ];
    }
}
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Models\Video;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationGroup = 'Site Yönetimi';
    protected static ?string $modelLabel = 'Video';
    protected static ?string $pluralModelLabel = 'Videolar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(12) // Toplam 12 kolonluk grid yapısı
                    ->schema([

                        // SOL TARAF (4 KOLON): Medya Dosyaları ve Yayın Durumu
                        Grid::make(1)
                            ->schema([
                                Section::make('Medya Yönetimi')
                                    ->description('Video dosyasını ve kapak resmini buradan yükleyin.')
                                    ->schema([
                                        FileUpload::make('video_file')
                                            ->label('Video Yükle (MP4)')
                                            ->directory('videos')
                                            ->disk('uploads')
                                            ->acceptedFileTypes(['video/mp4'])
                                            ->maxSize(102400) // 100MB Sınırı
                                            ->required()
                                            ->hint('Sadece MP4 formatı desteklenir.'),

                                        FileUpload::make('image')
                                            ->label('Kapak Fotoğrafı (Poster)')
                                            ->directory('videos')
                                            ->disk('uploads')
                                            ->image()
                                            ->imageEditor()
                                            ->helperText('Video henüz başlamadan önce görünecek olan sabit görsel.'),
                                    ]),

                                Section::make('Yayın Ayarları')
                                    ->schema([
                                        Toggle::make('is_published')
                                            ->label('Hemen Yayınla')
                                            ->default(true),
                                            
                                        TextInput::make('order')
                                            ->label('Sıralama')
                                            ->numeric()
                                            ->default(0)
                                            ->hint('Sitede hangi sırada görüneceğini belirler.'),
                                    ]),
                            ])
                            ->columnSpan(4),

                        // SAĞ TARAF (8 KOLON): Metin İçerikleri
                        Grid::make(1)
                            ->schema([
                                Section::make('İçerik Bilgileri')
                                    ->description('Video ile birlikte görünecek başlık ve açıklama metni.')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Video Başlığı')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Örn: Ali Baba Köfte Hazırlanış Hikayesi'),

                                        RichEditor::make('desc')
                                            ->label('Açıklama Metni')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'link',
                                                'bulletList',
                                                'orderedList',
                                                'redo',
                                                'undo',
                                            ])
                                            ->placeholder('Videonun altında görünecek detaylı açıklama yazısı...'),
                                    ]),
                            ])
                            ->columnSpan(8),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Kapak')
                    ->disk('uploads')
                    ->square(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('video_file')
                    ->label('Video Dosyası')
                    ->limit(20)
                    ->color('gray')
                    ->description(fn(Video $record): string => $record->video_file ? 'Dosya Mevcut' : 'Dosya Yok'),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Durum')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order')
                    ->label('Sıra')
                    ->sortable(),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Yayın Durumu'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}
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
use Filament\Forms\Components\Tabs;

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
                Grid::make(12) // Toplam 12 kolonluk bir grid başlatıyoruz
                    ->schema([

                        // SOL TARAF (4 KOLON): Medya ve Durum Yönetimi
                        Grid::make(1)
                            ->schema([
                                Section::make('Medya Yönetimi')
                                    ->schema([
                                        FileUpload::make('image')
                                            ->label('Kapak Fotoğrafı (Poster)')
                                            ->directory('videos')
                                            ->disk('uploads')
                                            ->image()
                                            ->helperText('Video başlamadan önce veya link modunda görünen görsel.'),

                                        Tabs::make('Video Kaynağı')
                                            ->tabs([
                                                Tabs\Tab::make('Dosya Yükle')
                                                    ->schema([
                                                        FileUpload::make('video_file')
                                                            ->label('MP4 Dosyası')
                                                            ->directory('videos')
                                                            ->disk('uploads')
                                                            ->acceptedFileTypes(['video/mp4'])
                                                            ->maxSize(51200),
                                                    ]),
                                                Tabs\Tab::make('Video Linki')
                                                    ->schema([
                                                        TextInput::make('link')
                                                            ->label('YouTube/Vimeo URL')
                                                            ->url(),
                                                    ]),
                                            ]),
                                    ]),

                                Section::make('Yayın Seçenekleri')
                                    ->schema([
                                        Toggle::make('is_published')
                                            ->label('Yayında')
                                            ->default(true),
                                        TextInput::make('order')
                                            ->label('Sıralama')
                                            ->numeric()
                                            ->default(0),
                                    ]),
                            ])
                            ->columnSpan(4), // Sol tarafa 4 kolon ayırdık

                        // SAĞ TARAF (8 KOLON): Başlık ve İçerik
                        Grid::make(1)
                            ->schema([
                                Section::make('İçerik Detayları')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Video Başlığı')
                                            ->required()
                                            ->maxLength(255),

                                        RichEditor::make('desc')
                                            ->label('Video Açıklaması')
                                            ->hint('Videonun sağ tarafında görünecek metin.')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'link',
                                                'bulletList',
                                                'orderedList'
                                            ])
                                            ->rows(10),
                                    ]),
                            ])
                            ->columnSpan(8), // Sağ tarafa 8 kolon ayırdık
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Görsel')
                    ->disk('uploads'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Durum')
                    ->boolean(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Sıra')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
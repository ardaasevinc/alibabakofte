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
                Section::make('Genel Bilgiler')
                    ->schema([
                        TextInput::make('title')
                            ->label('Başlık')
                            ->maxLength(255),
                        RichEditor::make('desc')
                            ->label('Açıklama')
                            ->toolbarButtons(['bold', 'italic', 'link']),
                    ]),

                Section::make('Video Kaynağı')
                    ->description('Videonuzu ister bir link olarak paylaşın, isterseniz doğrudan sunucuya yükleyin.')
                    ->schema([
                        Tabs::make('Video Type')
                            ->tabs([
                                Tabs\Tab::make('Video Linki (YouTube/Vimeo)')
                                    ->schema([
                                        TextInput::make('link')
                                            ->label('URL')
                                            ->placeholder('https://www.youtube.com/watch?v=...')
                                            ->url(),
                                    ]),
                                Tabs\Tab::make('Video Dosyası Yükle')
                                    ->schema([
                                        FileUpload::make('video_file')
                                            ->label('MP4 Dosyası')
                                            ->directory('videos')
                                            ->disk('uploads') // Belirttiğin disk yapısı
                                            ->acceptedFileTypes(['video/mp4', 'video/quicktime'])
                                            ->maxSize(51200) // 50MB sınır
                                            ->hint('Doğrudan oynatılacak mp4 dosyasını yükleyin.'),
                                    ]),
                            ]),
                    ]),

                Section::make('Görsel ve Durum')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Kapak Fotoğrafı (Poster)')
                            ->directory('videos')
                            ->disk('uploads')
                            ->image(),

                        Toggle::make('is_published')
                            ->label('Yayında mı?')
                            ->default(true),

                        TextInput::make('order')
                            ->label('Sıralama')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Kapak')
                    ->disk('uploads'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık'),
                Tables\Columns\TextColumn::make('link')
                    ->label('Kaynak')
                    ->formatStateUsing(fn($state, $record) => $state ? '🔗 Link' : ($record->video_file ? '📁 Dosya' : '-')),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label('Durum'),
            ])
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
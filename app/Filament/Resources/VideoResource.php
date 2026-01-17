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

    /**
     * Eğer veritabanında 1 adet kayıt varsa "Yeni Oluştur" butonunu gizler.
     */
    public static function canCreate(): bool
    {
        return Video::count() < 1;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(12)
                    ->schema([
                        // SOL TARAF (4 KOLON)
                        Grid::make(1)
                            ->schema([
                                Section::make('Medya Yönetimi')
                                    ->schema([
                                        FileUpload::make('video_file')
                                            ->label('Video Yükle (MP4)')
                                            ->directory('videos')
                                            ->disk('uploads')
                                            ->acceptedFileTypes(['video/mp4'])
                                            ->maxSize(102400)
                                            ->required(),

                                        FileUpload::make('image')
                                            ->label('Kapak Fotoğrafı (Poster)')
                                            ->directory('videos')
                                            ->disk('uploads')
                                            ->image(),
                                    ]),

                                Section::make('Durum')
                                    ->schema([
                                        Toggle::make('is_published')
                                            ->label('Yayında')
                                            ->default(true)
                                            ->helperText('Sitede görünmesi için aktif edin.'),
                                    ]),
                            ])
                            ->columnSpan(4),

                        // SAĞ TARAF (8 KOLON)
                        Grid::make(1)
                            ->schema([
                                Section::make('İçerik Detayları')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Video Başlığı')
                                            ->required(),
                                        RichEditor::make('desc')
                                            ->label('Açıklama Metni'),
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
                    ->disk('uploads'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık'),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Durum')
                    ->boolean(),
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
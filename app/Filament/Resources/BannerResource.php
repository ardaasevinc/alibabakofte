<?php


namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    // Singleton: Sadece 1 adet banner oluşturulabilir
    public static function canCreate(): bool
    {
        return Banner::count() < 1;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(12)
                    ->schema([
                        // SOL KOLON (4) - Görsel ve Durum
                        Forms\Components\Section::make('Görsel Alanı')
                            ->description('Ana sayfa banner görselini buradan yönetin.')
                            ->columnSpan(4)
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Banner Görseli')
                                    ->disk('uploads')
                                    ->directory('banners')
                                    ->image()
                                    ->required()
                                    ->helperText('Yüksek çözünürlüklü (geniş) bir görsel tercih edin.'),

                                Forms\Components\Toggle::make('is_published')
                                    ->label('Yayınla')
                                    ->default(true)
                                    ->helperText('Banner ana sayfada gösterilsin mi?'),
                            ]),

                        // SAĞ KOLON (8) - İçerik
                        Forms\Components\Section::make('Banner Detayları')
                            ->description('Başlık ve açıklama metni.')
                            ->columnSpan(8)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Banner Başlığı')
                                    ->required()
                                    ->helperText('Banner üzerinde görünecek büyük başlık.'),

                                Forms\Components\RichEditor::make('desc')
                                    ->label('Açıklama Metni')
                                    ->helperText('Başlığın altında yer alacak tanıtım yazısı.')
                                    ->fileAttachmentsDisk('uploads')
                                    ->fileAttachmentsDirectory('banner-content')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->disk('uploads'),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\IconColumn::make('is_published')->boolean()->label('Durum'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Sistem Ayarları';
    protected static ?string $modelLabel = 'Ayar';

    public static function canCreate(): bool
    {
        return Setting::count() < 1;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        // 1. SEKME: GENEL VE İLETİŞİM
                        Tab::make('Genel ve İletişim')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Grid::make(12)->schema([
                                    Section::make('Görseller')->columnSpan(4)->schema([
                                        FileUpload::make('logo_light')->label('Logo (Açık)')->disk('uploads')->directory('settings'),
                                        FileUpload::make('logo_dark')->label('Logo (Koyu)')->disk('uploads')->directory('settings'),
                                        FileUpload::make('favicon')->label('Favicon')->disk('uploads')->directory('settings'),
                                    ]),
                                    Section::make('İletişim Bilgileri')->columnSpan(8)->schema([
                                        TextInput::make('slogan'),
                                        Grid::make(2)->schema([
                                            TextInput::make('email')->email(),
                                            TextInput::make('phone'),
                                        ]),
                                        Textarea::make('address'),
                                        RichEditor::make('work_time')->columnSpanFull(),
                                    ]),
                                ]),
                            ]),

                        // 2. SEKME: SEO & SOSYAL
                        Tab::make('SEO & Sosyal Medya')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Grid::make(12)->schema([
                                    Section::make('SEO Ayarları')->columnSpan(4)->schema([
                                        TextInput::make('meta_title'),
                                        Textarea::make('meta_desc'),
                                        TextInput::make('meta_keywords'),
                                    ]),
                                    Section::make('Sosyal Medya & Linkler')->columnSpan(8)->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('facebook_url')->url(),
                                            TextInput::make('instagram_url')->url(),
                                            TextInput::make('map_link'),
                                            TextInput::make('gpage_link'),
                                        ]),
                                        TextInput::make('gpage_comment')->label('Google Yorum Linki')->columnSpanFull(),
                                        Textarea::make('map_iframe')->label('Harita Iframe')->rows(3),
                                    ]),
                                ]),
                            ]),

                        // 3. SEKME: ANALİZ & ENV (META & GOOGLE)
                        Tab::make('Analiz & İzleme (ENV)')
                            ->icon('heroicon-o-presentation-chart-line')
                            ->schema([
                                Section::make('API ve Takip ID Bilgileri')
                                    ->description('Bu bilgiler veritabanına kaydedilir ve otomatik olarak .env dosyanıza FACEBOOK_PIXEL_ID olarak işlenir.')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            // Veritabanındaki sütun adınızla eşleşmesi için facebook_pixel_code yapıldı
                                            TextInput::make('facebook_pixel_code')
                                                ->label('Facebook Pixel ID')
                                                ->placeholder('Örn: 1234567890'),
                                            
                                            TextInput::make('google_analytics_code')
                                                ->label('Google Analytics ID (G-...)')
                                                ->placeholder('G-XXXXXXXXXX'),
                                            
                                            Textarea::make('facebook_access_token')
                                                ->label('Meta CAPI Access Token')
                                                ->rows(3)
                                                ->columnSpanFull()
                                                ->helperText('Meta Business Suite üzerinden aldığınız CAPI tokenini buraya yapıştırın.'),
                                            
                                            TextInput::make('instagram_access_token')
                                                ->label('Instagram Access Token')
                                                ->columnSpanFull(),
                                        ]),
                                    ]),
                            ]),

                        // 4. SEKME: SİSTEM & MAİL
                        Tab::make('Sistem & Mail')
                            ->icon('heroicon-o-cpu-chip')
                            ->schema([
                                Grid::make(12)->schema([
                                    Section::make('Uygulama Bilgileri')->columnSpan(4)->schema([
                                        TextInput::make('app_url')->label('App URL'),
                                        Select::make('app_env')
                                            ->label('Çalışma Ortamı')
                                            ->options(['local' => 'Local', 'production' => 'Production']),
                                        Toggle::make('app_debug')->label('Debug Modu'),
                                    ]),
                                    Section::make('Mail (SMTP) Ayarları')->columnSpan(8)->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('mail_host'),
                                            TextInput::make('mail_port'),
                                            TextInput::make('mail_username'),
                                            TextInput::make('mail_password')->password()->revealable(),
                                            TextInput::make('mail_from_address'),
                                            TextInput::make('mail_from_name'),
                                        ]),
                                    ]),
                                ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')->label('E-Posta'),
                Tables\Columns\TextColumn::make('phone')->label('Telefon'),
                Tables\Columns\TextColumn::make('app_env')
                    ->label('Ortam')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'production' ? 'success' : 'danger'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
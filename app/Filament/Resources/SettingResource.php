<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Sistem Ayarları';

    public static function canCreate(): bool
    {
        return Setting::count() < 1;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        // 1. SEKME: GENEL VE İLETİŞİM
                        Forms\Components\Tabs\Tab::make('Genel ve İletişim')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Forms\Components\Grid::make(12)->schema([
                                    Forms\Components\Section::make('Kurumsal Görseller')
                                        ->columnSpan(4)
                                        ->schema([
                                            Forms\Components\FileUpload::make('logo_light')
                                                ->label('Logo (Açık Tema)')
                                                ->disk('uploads')->directory('settings')->image(),
                                            Forms\Components\FileUpload::make('logo_dark')
                                                ->label('Logo (Koyu Tema)')
                                                ->disk('uploads')->directory('settings')->image(),
                                            Forms\Components\FileUpload::make('favicon')
                                                ->label('Favicon')
                                                ->disk('uploads')->directory('settings')->image(),
                                        ]),
                                    Forms\Components\Section::make('İletişim ve Adres')
                                        ->columnSpan(8)
                                        ->schema([
                                            Forms\Components\TextInput::make('slogan')
                                                ->label('Slogan')->helperText('Site genelinde kullanılacak kısa yazı.'),
                                            Forms\Components\Grid::make(2)->schema([
                                                Forms\Components\TextInput::make('email')->email()->label('E-Posta'),
                                                Forms\Components\TextInput::make('phone')->label('Telefon'),
                                            ]),
                                            Forms\Components\Textarea::make('address')->label('Adres'),
                                            Forms\Components\RichEditor::make('work_time')
                                                ->label('Çalışma Saatleri')
                                                ->columnSpanFull(),
                                        ]),
                                ]),
                            ]),

                        // 2. SEKME: SEO VE SOSYAL MEDYA
                        Forms\Components\Tabs\Tab::make('SEO & Sosyal Medya')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\Grid::make(12)->schema([
                                    Forms\Components\Section::make('SEO Ayarları')
                                        ->columnSpan(4)
                                        ->schema([
                                            Forms\Components\TextInput::make('meta_title')->label('Meta Başlık'),
                                            Forms\Components\Textarea::make('meta_desc')->label('Meta Açıklama'),
                                            Forms\Components\TextInput::make('meta_keywords')->label('Anahtar Kelimeler'),
                                        ]),
                                    Forms\Components\Section::make('Sosyal Medya & Harita')
                                        ->columnSpan(8)
                                        ->schema([
                                            Forms\Components\Grid::make(2)->schema([
                                                Forms\Components\TextInput::make('facebook_url')->url()->label('Facebook'),
                                                Forms\Components\TextInput::make('instagram_url')->url()->label('Instagram'),
                                            ]),
                                            Forms\Components\Grid::make(2)->schema([
                                                Forms\Components\TextInput::make('map_link')->label('Google Harita Linki'),
                                                Forms\Components\TextInput::make('gpage_link')->label('Google İşletme Profili'),
                                            ]),
                                            Forms\Components\Textarea::make('map_iframe')->label('Harita Iframe Kodu')->rows(3),
                                            Forms\Components\TextInput::make('gpage_comment')
                                                ->label('Google Yorum Yap Linki')
                                                ->columnSpanFull(),
                                        ]),
                                ]),
                            ]),

                        // 3. SEKME: ANALİZ & TAKİP
                        Forms\Components\Tabs\Tab::make('Analiz & Takip')
                            ->icon('heroicon-o-presentation-chart-line')
                            ->schema([
                                Forms\Components\Grid::make(12)->schema([
                                    // META / FACEBOOK
                                    Forms\Components\Section::make('Meta (Facebook) Ayarları')
                                        ->columnSpan(6)
                                        ->schema([
                                            Forms\Components\TextInput::make('facebook_pixel_id')
                                                ->label('Facebook Pixel ID'),
                                            Forms\Components\Textarea::make('facebook_access_token')
                                                ->label('Facebook CAPI Access Token')
                                                ->rows(6),
                                        ]),
                                    // GOOGLE
                                    Forms\Components\Section::make('Google Ayarları')
                                        ->columnSpan(6)
                                        ->schema([
                                            Forms\Components\TextInput::make('google_analytics_id')
                                                ->label('Google Analytics ID (G-...)'),
                                            Forms\Components\TextInput::make('google_tag_manager_id')
                                                ->label('Google Tag Manager ID (GTM-...)'),
                                            Forms\Components\Textarea::make('google_tag_manager_noscript')
                                                ->label('GTM NoScript Kodu')
                                                ->rows(4)
                                                ->helperText('<body> etiketinden hemen sonra eklenir.'),
                                        ]),
                                ]),
                            ]),

                        // 4. SEKME: SİSTEM VE ENV
                        Forms\Components\Tabs\Tab::make('Sistem (ENV)')
                            ->icon('heroicon-o-cpu-chip')
                            ->schema([
                                Forms\Components\Grid::make(12)->schema([
                                    Forms\Components\Section::make('Uygulama Yapılandırması')
                                        ->columnSpan(4)
                                        ->schema([
                                            Forms\Components\TextInput::make('app_url')->label('APP_URL'),
                                            Forms\Components\Select::make('app_env')
                                                ->label('APP_ENV')
                                                ->options(['local' => 'Local', 'production' => 'Production']),
                                            Forms\Components\Toggle::make('app_debug')->label('APP_DEBUG'),
                                            Forms\Components\TextInput::make('instagram_access_token')->label('IG Access Token'),
                                        ]),
                                    Forms\Components\Section::make('E-Posta (SMTP)')
                                        ->columnSpan(8)
                                        ->schema([
                                            Forms\Components\Grid::make(2)->schema([
                                                Forms\Components\TextInput::make('mail_host')->label('Host'),
                                                Forms\Components\TextInput::make('mail_port')->label('Port'),
                                                Forms\Components\TextInput::make('mail_username')->label('Kullanıcı Adı'),
                                                Forms\Components\TextInput::make('mail_password')->password()->revealable()->label('Şifre'),
                                                Forms\Components\TextInput::make('mail_from_address')->label('Gönderen Mail'),
                                                Forms\Components\TextInput::make('mail_from_name')->label('Gönderen İsmi'),
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
                Tables\Columns\BadgeColumn::make('app_env')
                    ->colors(['danger' => 'local', 'success' => 'production']),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([]);
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
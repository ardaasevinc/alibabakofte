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

    // Sadece 1 kayıt olmasına izin veriyoruz
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
                            ->schema([
                                Forms\Components\Grid::make(12)->schema([
                                    // SOL 4 KOLON
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
                                    // SAĞ 8 KOLON
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
                                                ->helperText('Hangi günler ve saatler hizmet veriliyor?')
                                                ->columnSpanFull(),
                                        ]),
                                ]),
                            ]),

                        // 2. SEKME: SEO VE SOSYAL MEDYA
                       // SettingResource.php içindeki ilgili sekme bölümü

// 2. SEKME: SEO VE SOSYAL MEDYA
Forms\Components\Tabs\Tab::make('SEO & Sosyal Medya')
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
                        Forms\Components\TextInput::make('map_link')
                            ->label('Google Harita Linki')
                            ->placeholder('Yol tarifi linki...'),
                        Forms\Components\TextInput::make('gpage_link')
                            ->label('Google İşletme Profili')
                            ->placeholder('İşletme sayfası ana linki...'),
                    ]),

                    Forms\Components\Textarea::make('map_iframe')
                        ->label('Harita Iframe Kodu')
                        ->placeholder('<iframe src="..."></iframe>')
                        ->rows(3),

                    // YENİ EKLENEN ALAN
                    Forms\Components\TextInput::make('gpage_comment')
                        ->label('Google Yorum Yap Linki')
                        ->helperText('Müşterilerin doğrudan yorum yazma ekranına gitmesini sağlayan link.')
                        ->columnSpanFull()
                        ->placeholder('https://g.page/r/your-id/review'),
                ]),
        ]),
    ]),

                        // 3. SEKME: SİSTEM VE ENV (HASSAS AYARLAR)
                       Forms\Components\Tabs\Tab::make('Sistem (ENV)')
    ->schema([
        Forms\Components\Grid::make(12)->schema([
            // SOL KOLON (4): Uygulama Temel Ayarları
            Forms\Components\Section::make('Uygulama Yapılandırması')
                ->description('Temel uygulama ve Instagram API ayarları.')
                ->columnSpan(4)
                ->schema([
                    Forms\Components\TextInput::make('app_url')
                        ->label('APP_URL')
                        ->default(config('app.url'))
                        ->helperText('Uygulamanın tam adresi (https://domain.com)'),
                    
                    Forms\Components\Select::make('app_env')
                        ->label('APP_ENV')
                        ->options([
                            'local' => 'Local (Geliştirme)',
                            'production' => 'Production (Canlı)',
                        ])
                        ->default('production'),
                    
                    Forms\Components\Toggle::make('app_debug')
                        ->label('APP_DEBUG (Hata Ayıklama)')
                        ->default(false)
                        ->helperText('Canlı sistemde kapalı tutulması önerilir.'),
                    
                    Forms\Components\TextInput::make('instagram_access_token')
                        ->label('IG Access Token')
                        ->placeholder('Token giriniz...'),
                    
                    Forms\Components\TextInput::make('instagram_app_secret')
                        ->label('IG App Secret')
                        ->password()
                        ->revealable(),
                ]),

            // SAĞ KOLON (8): Mail Ayarları
            Forms\Components\Section::make('E-Posta Sunucu Ayarları (SMTP)')
                ->description('Sistem üzerinden gönderilecek e-postaların sunucu ayarları.')
                ->columnSpan(8)
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('mail_mailer')
                            ->label('Mailer')
                            ->default('smtp')
                            ->helperText('Örn: smtp, log, mailgun'),

                        Forms\Components\TextInput::make('mail_host')
                            ->label('Host')
                            ->placeholder('smtp.mailtrap.io')
                            ->helperText('Mail sunucu adresi.'),

                        Forms\Components\TextInput::make('mail_port')
                            ->label('Port')
                            ->default('587')
                            ->placeholder('587 veya 465'),

                        Forms\Components\TextInput::make('mail_username')
                            ->label('Kullanıcı Adı')
                            ->placeholder('info@domain.com'),

                        Forms\Components\TextInput::make('mail_password')
                            ->label('Şifre')
                            ->password()
                            ->revealable(),

                        Forms\Components\TextInput::make('mail_from_address')
                            ->label('Gönderen Adresi')
                            ->default('hello@example.com')
                            ->helperText('Giden maillerde görünecek e-posta.'),

                        Forms\Components\TextInput::make('mail_from_name')
                            ->label('Gönderen İsmi')
                            ->default(config('app.name'))
                            ->helperText('Giden maillerde görünecek isim.'),
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
                Tables\Columns\TextColumn::make('app_url')->label('Site URL'),
                Tables\Columns\BadgeColumn::make('app_env')
                    ->colors([
                        'danger' => 'local',
                        'success' => 'production',
                    ]),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
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
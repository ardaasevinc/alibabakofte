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
                        // 1. SEKME: GENEL
                        Forms\Components\Tabs\Tab::make('Genel ve İletişim')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Forms\Components\Grid::make(12)->schema([
                                    Forms\Components\Section::make('Görseller')->columnSpan(4)->schema([
                                        Forms\Components\FileUpload::make('logo_light')->label('Logo (Açık)')->disk('uploads')->directory('settings'),
                                        Forms\Components\FileUpload::make('logo_dark')->label('Logo (Koyu)')->disk('uploads')->directory('settings'),
                                        Forms\Components\FileUpload::make('favicon')->label('Favicon')->disk('uploads')->directory('settings'),
                                    ]),
                                    Forms\Components\Section::make('İletişim Bilgileri')->columnSpan(8)->schema([
                                        Forms\Components\TextInput::make('slogan'),
                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('email')->email(),
                                            Forms\Components\TextInput::make('phone'),
                                        ]),
                                        Forms\Components\Textarea::make('address'),
                                        Forms\Components\RichEditor::make('work_time')->columnSpanFull(),
                                    ]),
                                ]),
                            ]),

                        // 2. SEKME: SEO & SOSYAL
                        Forms\Components\Tabs\Tab::make('SEO & Sosyal Medya')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\Grid::make(12)->schema([
                                    Forms\Components\Section::make('SEO Ayarları')->columnSpan(4)->schema([
                                        Forms\Components\TextInput::make('meta_title'),
                                        Forms\Components\Textarea::make('meta_desc'),
                                        Forms\Components\TextInput::make('meta_keywords'),
                                    ]),
                                    Forms\Components\Section::make('Sosyal Medya & Linkler')->columnSpan(8)->schema([
                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('facebook_url')->url(),
                                            Forms\Components\TextInput::make('instagram_url')->url(),
                                            Forms\Components\TextInput::make('map_link'),
                                            Forms\Components\TextInput::make('gpage_link'),
                                        ]),
                                        Forms\Components\TextInput::make('gpage_comment')->label('Google Yorum Linki')->columnSpanFull(),
                                        Forms\Components\Textarea::make('map_iframe')->label('Harita Iframe')->rows(3),
                                    ]),
                                ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Analiz & Takip')
                            ->icon('heroicon-o-presentation-chart-line')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\Section::make('Meta (Facebook) Pixel')
                                        ->description('Buraya <script> ile başlayan tüm kodu yapıştırın.')
                                        ->schema([
                                            Forms\Components\Textarea::make('facebook_pixel_code')
                                                ->label('Pixel Kodu')
                                                ->rows(15) // Kutu boyunu uzattık
                                                ->placeholder('...'),
                                        ]),
                                    Forms\Components\Section::make('Google Analytics')
                                        ->description('Buraya G- ile başlayan Google etiket kodunu yapıştırın.')
                                        ->schema([
                                            Forms\Components\Textarea::make('google_analytics_code')
                                                ->label('Analytics Kodu')
                                                ->rows(15) // Kutu boyunu uzattık
                                                ->placeholder('...'),
                                        ]),
                                    Forms\Components\Textarea::make('facebook_access_token')
                                        ->label('Facebook CAPI Access Token')
                                        ->rows(5)
                                        ->placeholder('EAAb...')
                                        ->helperText('Meta panelinden oluşturduğunuz uzun erişim jetonunu buraya yapıştırın.')
                                        ->columnSpanFull(),
                                ]),
                            ]),

                        // 4. SEKME: SİSTEM
                        Forms\Components\Tabs\Tab::make('Sistem (ENV)')
                            ->icon('heroicon-o-cpu-chip')
                            ->schema([
                                Forms\Components\Grid::make(12)->schema([
                                    Forms\Components\Section::make('Uygulama')->columnSpan(4)->schema([
                                        Forms\Components\TextInput::make('app_url'),
                                        Forms\Components\Select::make('app_env')->options(['local' => 'Local', 'production' => 'Production']),
                                        Forms\Components\Toggle::make('app_debug'),
                                        Forms\Components\TextInput::make('instagram_access_token'),
                                    ]),
                                    Forms\Components\Section::make('Mail (SMTP)')->columnSpan(8)->schema([
                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('mail_host'),
                                            Forms\Components\TextInput::make('mail_port'),
                                            Forms\Components\TextInput::make('mail_username'),
                                            Forms\Components\TextInput::make('mail_password')->password()->revealable(),
                                            Forms\Components\TextInput::make('mail_from_address'),
                                            Forms\Components\TextInput::make('mail_from_name'),
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
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\BadgeColumn::make('app_env')->colors(['danger' => 'local', 'success' => 'production']),
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
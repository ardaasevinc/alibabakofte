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

    public static function canCreate(): bool { return Setting::count() < 1; }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Genel ve İletişim')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Forms\Components\Grid::make(12)->schema([
                                    Forms\Components\Section::make('Görseller')->columnSpan(4)->schema([
                                        Forms\Components\FileUpload::make('logo_light')->disk('uploads')->directory('settings')->image(),
                                        Forms\Components\FileUpload::make('logo_dark')->disk('uploads')->directory('settings')->image(),
                                        Forms\Components\FileUpload::make('favicon')->disk('uploads')->directory('settings')->image(),
                                    ]),
                                    Forms\Components\Section::make('İletişim')->columnSpan(8)->schema([
                                        Forms\Components\TextInput::make('slogan')->label('Slogan'),
                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('email')->email(),
                                            Forms\Components\TextInput::make('phone'),
                                        ]),
                                        Forms\Components\Textarea::make('address'),
                                        Forms\Components\RichEditor::make('work_time')->columnSpanFull(),
                                    ]),
                                ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('SEO & Sosyal Medya')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\Grid::make(12)->schema([
                                    Forms\Components\Section::make('SEO')->columnSpan(4)->schema([
                                        Forms\Components\TextInput::make('meta_title'),
                                        Forms\Components\Textarea::make('meta_desc'),
                                        Forms\Components\TextInput::make('meta_keywords'),
                                    ]),
                                    Forms\Components\Section::make('Linkler')->columnSpan(8)->schema([
                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('facebook_url')->url(),
                                            Forms\Components\TextInput::make('instagram_url')->url(),
                                            Forms\Components\TextInput::make('map_link'),
                                            Forms\Components\TextInput::make('gpage_link'),
                                        ]),
                                        Forms\Components\Textarea::make('map_iframe')->rows(3),
                                        Forms\Components\TextInput::make('gpage_comment')->columnSpanFull(),
                                    ]),
                                ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Analiz & Takip')
                            ->icon('heroicon-o-presentation-chart-line')
                            ->schema([
                                Forms\Components\Grid::make(12)->schema([
                                    Forms\Components\Section::make('Meta (Facebook)')->columnSpan(6)->schema([
                                        Forms\Components\TextInput::make('facebook_pixel_id')->label('Pixel ID'),
                                        Forms\Components\Textarea::make('facebook_access_token')->label('CAPI Token')->rows(6),
                                    ]),
                                    Forms\Components\Section::make('Google')->columnSpan(6)->schema([
                                        Forms\Components\TextInput::make('google_analytics_id')->label('Analytics ID'),
                                        Forms\Components\TextInput::make('google_tag_manager_id')->label('GTM ID'),
                                        Forms\Components\Textarea::make('google_tag_manager_noscript')->label('GTM NoScript')->rows(4),
                                    ]),
                                ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Sistem (ENV)')
                            ->icon('heroicon-o-cpu-chip')
                            ->schema([
                                Forms\Components\Grid::make(12)->schema([
                                    Forms\Components\Section::make('App Config')->columnSpan(4)->schema([
                                        Forms\Components\TextInput::make('app_url'),
                                        Forms\Components\Select::make('app_env')->options(['local'=>'Local','production'=>'Production']),
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
                Tables\Columns\BadgeColumn::make('app_env')->colors(['danger'=>'local','success'=>'production']),
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
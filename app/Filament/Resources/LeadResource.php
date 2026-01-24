<?php

namespace App\Filament\Resources;

use App\Models\Lead;
use Filament\Resources\Resource;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\LeadResource\Pages;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Dönüşüm Takibi';
    protected static ?string $pluralModelLabel = 'Dönüşümler (CAPI)';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            
            // 1. ÖZET VE DURUM
            Section::make('Dönüşüm Özeti')
                ->schema([
                    Grid::make(4)->schema([
                        TextEntry::make('type')->label('Eylem Tipi')->badge()->color('success'),
                        TextEntry::make('event_name')->label('Meta Event'),
                        TextEntry::make('button_id')->label('Buton ID')->placeholder('-'),
                        TextEntry::make('created_at')->label('Kayıt Tarihi')->dateTime('d M Y H:i:s'),
                    ]),
                ]),

            // 2. PAZARLAMA VE TRAFİK (UTM & REKLAM)
            Section::make('Pazarlama ve Reklam Parametreleri')
                ->description('UTM kaynakları ve reklam tıklama kimlikleri')
                ->collapsible()
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('utm_source')->label('UTM Source')->badge()->color('info'),
                        TextEntry::make('utm_medium')->label('UTM Medium')->badge(),
                        TextEntry::make('utm_campaign')->label('UTM Campaign')->badge(),
                        TextEntry::make('utm_term')->label('UTM Term'),
                        TextEntry::make('utm_content')->label('UTM Content'),
                    ]),
                    Grid::make(2)->schema([
                        TextEntry::make('fbclid')->label('Facebook Click ID (fbclid)')->copyable()->fontFamily('mono')->color('primary'),
                        TextEntry::make('gclid')->label('Google Click ID (gclid)')->copyable()->fontFamily('mono')->color('warning'),
                    ]),
                ]),

            // 3. META CAPI KİMLİKLERİ
            Section::make('Meta Tarayıcı ve Eşleşme Verileri')
                ->collapsible()
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('event_id')->label('Event ID')->copyable()->fontFamily('mono'),
                        TextEntry::make('external_id')->label('External ID')->copyable()->fontFamily('mono'),
                        TextEntry::make('fbp')->label('_fbp')->copyable()->fontFamily('mono'),
                        TextEntry::make('fbc')->label('_fbc')->copyable()->fontFamily('mono'),
                    ]),
                ]),

            // 4. TEKNİK VE CİHAZ BİLGİLERİ
            Section::make('Cihaz ve Bağlantı Bilgileri')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('ip_address')->label('IP Adresi'),
                        TextEntry::make('platform')->label('İşletim Sistemi')->badge(),
                        TextEntry::make('is_mobile')->label('Mobil Cihaz mı?')
                            ->formatStateUsing(fn($state) => $state ? 'Evet' : 'Hayır')
                            ->badge()->color(fn($state) => $state ? 'success' : 'gray'),
                    ]),
                    TextEntry::make('user_agent')->label('User Agent')->size('xs')->color('gray'),
                ]),

            // 5. URL GEÇMİŞİ
            Section::make('Navigasyon Geçmişi')
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('landing_page')->label('Giriş Sayfası (Landing)')->copyable(),
                    TextEntry::make('came_from_url')->label('Geldiği URL'),
                    TextEntry::make('referer')->label('Referer (Yönlendiren)'),
                    TextEntry::make('event_source_url')->label('Event Kaynak URL (CAPI)'),
                ]),

            // 6. JSON LOGLARI (Hata Almamak İçin Accessor Kullanan Bölüm)
            Section::make('Meta CAPI Logları (Ham Veri)')
                ->description('Meta API tarafına giden ve dönen tüm ham JSON paketleri')
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('payload_json')
                        ->label('Payload (Cihaz & Tarayıcı Detayı)')
                        ->extraAttributes(['class' => 'font-mono text-xs bg-gray-950 p-4 rounded-lg text-green-400'])
                        ->columnSpanFull(),

                    TextEntry::make('request_json')
                        ->label('Meta Request (Gönderilen Paket)')
                        ->extraAttributes(['class' => 'font-mono text-xs bg-black p-4 rounded-lg text-blue-400'])
                        ->columnSpanFull(),

                    TextEntry::make('response_json')
                        ->label('Meta Response (Meta Yanıtı)')
                        ->extraAttributes(['class' => 'font-mono text-xs bg-black p-4 rounded-lg text-purple-400'])
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Zaman')->dateTime('H:i | d.m.Y')->sortable(),
                TextColumn::make('type')->label('Eylem')->badge()->color('success'),
                TextColumn::make('utm_source')->label('Kaynak')->placeholder('Organik'),
                TextColumn::make('fbclid')->label('Meta Ads')
                    ->formatStateUsing(fn($state) => $state ? 'Reklam' : 'Değil')
                    ->badge()->color(fn($state) => $state ? 'primary' : 'gray'),
                TextColumn::make('ip_address')->label('IP')->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'view' => Pages\ViewLead::route('/{record}'),
        ];
    }
}
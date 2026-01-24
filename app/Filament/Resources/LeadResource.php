<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Dönüşüm Takibi';
    protected static ?string $modelLabel = 'Dönüşüm Kaydı';
    protected static ?string $pluralModelLabel = 'Dönüşümler (CAPI)';

    public static function form(Form $form): Form
    {
        return $form->schema([]); // Manual create yok
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            /* ============================================================
             |  DÖNÜŞÜM ÖZETİ (type, zaman, kaynak)
             ============================================================ */
            Section::make('Dönüşüm Özeti')
                ->schema([
                    Grid::make(4)->schema([

                        TextEntry::make('type')
                            ->label('Dönüşüm Tipi')
                            ->badge()
                            ->icon(fn($state) =>
                                $state === 'whatsapp'
                                    ? 'heroicon-m-chat-bubble-left-right'
                                    : 'heroicon-m-list-bullet'
                            )
                            ->color(fn($state) =>
                                $state === 'whatsapp' ? 'success' : 'warning'
                            )
                            ->formatStateUsing(fn($state) =>
                                $state === 'whatsapp' ? 'WhatsApp' : 'Menü'
                            ),

                        TextEntry::make('utm_source')
                            ->label('Kaynak')
                            ->badge()
                            ->placeholder('Direct / Organik')
                            ->color('info'),

                        TextEntry::make('utm_campaign')
                            ->label('Kampanya')
                            ->placeholder('-')
                            ->weight(FontWeight::SemiBold),

                        TextEntry::make('created_at')
                            ->label('Tarih')
                            ->dateTime('d M Y H:i:s'),
                    ]),
                ]),

            /* ============================================================
             |  TRAFİK PARAMETRELERİ (utm’ler, fbclid, gclid)
             ============================================================ */
            Section::make('Trafik Parametreleri')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make(3)->schema([

                        TextEntry::make('utm_source')
                            ->label('utm_source')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('utm_medium')
                            ->label('utm_medium')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('utm_campaign')
                            ->label('utm_campaign')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('fbclid')
                            ->label('fbclid')
                            ->copyable()
                            ->color(fn($state) => $state ? 'success' : 'gray'),

                        TextEntry::make('gclid')
                            ->label('gclid')
                            ->copyable()
                            ->color(fn($state) => $state ? 'warning' : 'gray'),

                    ]),
                ]),

            /* ============================================================
             |  META CAPI PARAMETRELERİ (event_id, fbp, fbc vs.)
             ============================================================ */
            Section::make('Meta CAPI Parametreleri')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make(2)->schema([

                        TextEntry::make('event_id')
                            ->label('Event ID')
                            ->copyable()
                            ->fontFamily('mono'),

                        TextEntry::make('fbc')
                            ->label('FBC')
                            ->copyable()
                            ->fontFamily('mono'),

                        TextEntry::make('fbp')
                            ->label('FBP')
                            ->copyable()
                            ->fontFamily('mono'),

                        TextEntry::make('browser_id')
                            ->label('Browser ID')
                            ->copyable()
                            ->fontFamily('mono'),

                        TextEntry::make('device_id')
                            ->label('Device ID')
                            ->copyable()
                            ->columnSpanFull()
                            ->fontFamily('mono'),

                        TextEntry::make('session_hash')
                            ->label('Session Hash')
                            ->copyable()
                            ->columnSpanFull()
                            ->fontFamily('mono'),
                    ]),
                ]),

            /* ============================================================
             |  ZİYARETÇİ TEKNİK VERİLERİ
             ============================================================ */
            Section::make('Cihaz ve Teknik Veriler')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make(3)->schema([

                        TextEntry::make('ip_address')
                            ->label('IP Adresi')
                            ->icon('heroicon-m-globe-alt'),

                        TextEntry::make('platform')
                            ->label('Platform')
                            ->badge()
                            ->color('primary')
                            ->formatStateUsing(fn($state) => ucfirst($state)),

                        TextEntry::make('is_mobile')
                            ->label('Cihaz Türü')
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'gray')
                            ->formatStateUsing(fn($state) =>
                                $state ? 'Mobil' : 'Desktop'
                            ),

                        TextEntry::make('referer')
                            ->label('Referer')
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('landing_page')
                            ->label('Geldiği URL')
                            ->url(fn($state) => $state)
                            ->openUrlInNewTab()
                            ->columnSpanFull(),
                    ]),
                ]),

            /* ============================================================
             |  PAYLOAD JSON
             ============================================================ */
            Section::make('Payload JSON')
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('payload')
                        ->label('Payload')
                        ->formatStateUsing(fn($state) =>
                            blank($state)
                                ? 'Ek veri yok'
                                : json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                        )
                        ->copyable()
                        ->extraAttributes([
                            'class' => 'whitespace-pre-wrap text-xs font-mono bg-gray-100 dark:bg-gray-900 p-3 rounded-lg',
                        ])
                        ->columnSpanFull(),
                ]),
        ]);
    }

    /* ============================================================
     |  TABLE
     ============================================================ */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('created_at')
                    ->label('Zaman')
                    ->dateTime('H:i | d.m.Y')
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Eylem')
                    ->badge()
                    ->formatStateUsing(fn($state) =>
                        $state === 'whatsapp' ? 'WhatsApp' : 'Menü'
                    )
                    ->color(fn($state) =>
                        $state === 'whatsapp' ? 'success' : 'warning'
                    ),

                TextColumn::make('utm_source')
                    ->label('Kaynak')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('fbclid')
                    ->label('Reklam')
                    ->formatStateUsing(fn($state) =>
                        $state ? 'Meta Ads' : 'Organik'
                    )
                    ->badge()
                    ->color(fn($state) =>
                        $state ? 'success' : 'gray'
                    ),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    /* ============================================================
     |  PAGES
     ============================================================ */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'view'  => Pages\ViewLead::route('/{record}'),
        ];
    }
}

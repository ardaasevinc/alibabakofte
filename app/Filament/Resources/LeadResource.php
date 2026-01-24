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
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            /* ============================================================
             |  DÖNÜŞÜM ÖZETİ
             ============================================================ */
            Section::make('Dönüşüm Özeti')
                ->schema([
                    Grid::make(4)->schema([

                        TextEntry::make('type')
                            ->label('Dönüşüm Tipi')
                            ->badge()
                            ->icon(fn($state) => $state === 'whatsapp'
                                ? 'heroicon-m-chat-bubble-left-right'
                                : 'heroicon-m-list-bullet')
                            ->color(fn($state) => $state === 'whatsapp' ? 'success' : 'warning')
                            ->formatStateUsing(fn($state) => $state === 'whatsapp' ? 'WhatsApp' : 'Menü'),

                        TextEntry::make('utm_source')
                            ->label('Kaynak')
                            ->badge()
                            ->color('info')
                            ->formatStateUsing(fn($state) => is_array($state) ? json_encode($state) : $state),

                        TextEntry::make('utm_campaign')
                            ->label('Kampanya')
                            ->placeholder('-')
                            ->weight(FontWeight::SemiBold)
                            ->formatStateUsing(fn($state) => is_array($state) ? json_encode($state) : $state),

                        TextEntry::make('created_at')
                            ->label('Tarih')
                            ->dateTime('d M Y H:i:s'),
                    ]),
                ]),

            /* ============================================================
             |  TRAFİK PARAMETRELERİ
             ============================================================ */
            Section::make('Trafik Parametreleri')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make(3)->schema([

                        TextEntry::make('utm_source')
                            ->label('utm_source')
                            ->badge()
                            ->color('info')
                            ->formatStateUsing(fn($state) => is_array($state) ? json_encode($state) : $state),

                        TextEntry::make('utm_medium')
                            ->label('utm_medium')
                            ->badge()
                            ->color('info')
                            ->formatStateUsing(fn($state) => is_array($state) ? json_encode($state) : $state),

                        TextEntry::make('utm_campaign')
                            ->label('utm_campaign')
                            ->badge()
                            ->color('info')
                            ->formatStateUsing(fn($state) => is_array($state) ? json_encode($state) : $state),

                        TextEntry::make('fbclid')
                            ->copyable()
                            ->color(fn($state) => $state ? 'success' : 'gray'),

                        TextEntry::make('gclid')
                            ->copyable()
                            ->color(fn($state) => $state ? 'warning' : 'gray'),
                    ]),
                ]),

            /* ============================================================
             |  META CAPI PARAMETRELERİ
             ============================================================ */
            Section::make('Meta CAPI Parametreleri')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make(2)->schema([

                        TextEntry::make('event_id')
                            ->copyable()
                            ->fontFamily('mono'),

                        TextEntry::make('fbc')
                            ->copyable()
                            ->fontFamily('mono'),

                        TextEntry::make('fbp')
                            ->copyable()
                            ->fontFamily('mono'),

                        TextEntry::make('browser_id')
                            ->copyable()
                            ->fontFamily('mono'),

                        TextEntry::make('device_id')
                            ->copyable()
                            ->fontFamily('mono')
                            ->columnSpanFull(),

                        TextEntry::make('session_hash')
                            ->copyable()
                            ->fontFamily('mono')
                            ->columnSpanFull(),
                    ]),
                ]),

            /* ============================================================
             |  CİHAZ VE TEKNİK VERİLER
             ============================================================ */
            Section::make('Cihaz ve Teknik Veriler')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make(3)->schema([

                        TextEntry::make('ip_address')
                            ->label('IP Adresi'),

                        TextEntry::make('platform')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('is_mobile')
                            ->badge()
                            ->color(fn($state) => $state ? 'success' : 'gray')
                            ->formatStateUsing(fn($state) => $state ? 'Mobil' : 'Desktop'),

                        TextEntry::make('referer')
                            ->columnSpanFull()
                            ->formatStateUsing(fn($state) => is_array($state) ? json_encode($state) : $state),

                        TextEntry::make('landing_page')
                            ->columnSpanFull()
                            ->url(fn($state) => is_string($state) ? $state : null)
                            ->openUrlInNewTab()
                            ->formatStateUsing(fn($state) => is_array($state) ? json_encode($state) : $state),
                    ]),
                ]),

            /* ============================================================
             |  PAYLOAD JSON — FİLAMENT 3.3 UYUMLU
             ============================================================ */
            Section::make('Payload')
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('payload')
                        ->label('Payload JSON')
                        ->formatStateUsing(fn($state) => json_encode($state, JSON_PRETTY_PRINT))
                        ->copyable()
                        ->columnSpanFull()
                        ->extraAttributes([
                            'style' =>
                                'white-space: pre-wrap;
                                 font-family: monospace;
                                 font-size: 14px;
                                 background:#0f0f0f;
                                 color:#eee;
                                 padding:16px;
                                 border-radius:8px;'
                        ]),
                ]),
        ]);
    }

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
                    ->formatStateUsing(fn($state) => $state === 'whatsapp' ? 'WhatsApp' : 'Menü')
                    ->color(fn($state) => $state === 'whatsapp' ? 'success' : 'warning'),

                TextColumn::make('utm_source')
                    ->label('Kaynak')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->formatStateUsing(fn($state) => is_array($state) ? json_encode($state) : $state),

                TextColumn::make('fbclid')
                    ->label('Reklam')
                    ->formatStateUsing(fn($state) => $state ? 'Meta Ads' : 'Organik')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'gray'),
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

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
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
        // Elle lead oluşturulmaz.
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Section::make('Dönüşüm Bilgisi')
                ->description('Meta CAPI tarafından kaydedilen dönüşüm detayları')
                ->schema([
                    Grid::make(4)->schema([

                        TextEntry::make('type')
                            ->label('Dönüşüm Tipi')
                            ->badge()
                            ->color(fn($val) => $val === 'whatsapp' ? 'success' : 'warning')
                            ->formatStateUsing(fn($val) => $val === 'whatsapp' ? 'WhatsApp' : 'Menü')
                            ->icon(fn($val) => $val === 'whatsapp'
                                ? 'heroicon-m-chat-bubble-left-right'
                                : 'heroicon-m-list-bullet'),

                        TextEntry::make('utm_source')
                            ->label('utm_source')
                            ->badge()
                            ->placeholder('Direct / Organik')
                            ->color('info')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('utm_campaign')
                            ->label('utm_campaign')
                            ->placeholder('-'),

                        TextEntry::make('created_at')
                            ->label('Tarih')
                            ->dateTime('d.m.Y H:i')
                            ->color('gray'),

                    ])
                ]),

            Section::make('Meta Eşleştirme Parametreleri')
                ->collapsed()
                ->collapsible()
                ->schema([
                    Grid::make(2)->schema([

                        TextEntry::make('event_id')
                            ->label('Event ID (Dedup)')
                            ->copyable()
                            ->fontFamily('mono'),

                        TextEntry::make('fbclid')
                            ->label('fbclid')
                            ->badge()
                            ->color(fn($val) => $val ? 'success' : 'gray')
                            ->placeholder('FB Ads tıklaması yok'),

                        TextEntry::make('fbc')
                            ->label('fbc')
                            ->copyable()
                            ->fontFamily('mono')
                            ->placeholder('-'),

                        TextEntry::make('fbp')
                            ->label('fbp')
                            ->copyable()
                            ->fontFamily('mono')
                            ->placeholder('-'),

                        TextEntry::make('device_id')
                            ->label('Hashed Device ID')
                            ->copyable()
                            ->fontFamily('mono')
                            ->columnSpanFull(),

                        TextEntry::make('session_hash')
                            ->label('Session Hash')
                            ->copyable()
                            ->fontFamily('mono')
                            ->columnSpanFull(),
                    ])
                ]),

            Section::make('Ziyaretçi Teknik Bilgileri')
                ->collapsed()
                ->collapsible()
                ->schema([
                    Grid::make(2)->schema([

                        TextEntry::make('ip_address')
                            ->label('IP Adresi')
                            ->icon('heroicon-m-globe-alt'),

                        TextEntry::make('browser_id')
                            ->label('Browser ID')
                            ->placeholder('-')
                            ->copyable()
                            ->fontFamily('mono'),

                        TextEntry::make('referer')
                            ->label('Referer')
                            ->columnSpanFull()
                            ->placeholder('Yok'),

                        TextEntry::make('landing_page')
                            ->label('Geldiği Sayfa')
                            ->url(fn($state) => $state)
                            ->openUrlInNewTab()
                            ->icon('heroicon-m-link')
                            ->columnSpanFull(),
                    ])
                ]),

            Section::make('Payload (Ek Veri)')
                ->collapsed()
                ->collapsible()
                ->schema([
                    TextEntry::make('payload')
                        ->label('Payload JSON')
                        ->formatStateUsing(function ($state) {
                            if (!$state) {
                                return 'Ek veri yok';
                            }
                            return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        })
                        ->copyable()
                        ->extraAttributes([
                            'class' => 'whitespace-pre-wrap text-xs font-mono',
                        ])
                        ->columnSpanFull(),
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
                    ->sortable()
                    ->description(fn($record) => $record->created_at->diffForHumans()),

                TextColumn::make('type')
                    ->label('Eylem')
                    ->badge()
                    ->formatStateUsing(fn($s) => $s === 'whatsapp' ? 'WhatsApp' : 'Menü')
                    ->color(fn($s) => $s === 'whatsapp' ? 'success' : 'warning')
                    ->icon(fn($s) => $s === 'whatsapp'
                        ? 'heroicon-m-chat-bubble-left-right'
                        : 'heroicon-m-list-bullet'),

                TextColumn::make('utm_source')
                    ->label('Kaynak')
                    ->badge()
                    ->color('info')
                    ->placeholder('Direct / Organik')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('fbclid')
                    ->label('Reklam')
                    ->badge()
                    ->formatStateUsing(fn($s) => $s ? 'Meta Ads' : 'Organik')
                    ->color(fn($s) => $s ? 'success' : 'gray'),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([

                Tables\Filters\SelectFilter::make('type')
                    ->label('Dönüşüm Tipi')
                    ->options([
                        'menu' => 'Menü',
                        'whatsapp' => 'WhatsApp',
                    ]),

                Tables\Filters\TernaryFilter::make('fbclid')
                    ->label('Trafik')
                    ->placeholder('Tümü')
                    ->trueLabel('Reklam (FB Ads)')
                    ->falseLabel('Organik Trafik')
                    ->queries(
                        true: fn($q) => $q->whereNotNull('fbclid'),
                        false: fn($q) => $q->whereNull('fbclid'),
                    ),
            ])

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->icon('heroicon-m-trash'),
                ]),
            ])

            ->emptyStateHeading('Henüz dönüşüm yok')
            ->emptyStateDescription('Meta CAPI veya site etkileşimlerinden gelen dönüşümler burada listelenir.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'view' => Pages\ViewLead::route('/{record}'),
        ];
    }
}

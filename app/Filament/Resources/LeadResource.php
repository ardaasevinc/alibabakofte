<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Dönüşüm Takibi';
    protected static ?string $modelLabel = 'Dönüşüm Kaydı';
    protected static ?string $pluralModelLabel = 'Dönüşümler (CAPI)';

    public static function form(Form $form): Form
    {
        return $form->schema([]); // Lead manuel oluşturulmaz
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Section::make('Dönüşüm Özeti')
                ->description('Meta CAPI tarafından alınan dönüşüm bilgileri')
                ->schema([
                    Grid::make(4)->schema([

                        TextEntry::make('type')
                            ->label('Dönüşüm Tipi')
                            ->badge()
                            ->icon(
                                fn($state) => $state === 'whatsapp'
                                ? 'heroicon-m-chat-bubble-left-right'
                                : 'heroicon-m-list-bullet'
                            )
                            ->color(fn($state) => $state === 'whatsapp' ? 'success' : 'warning')
                            ->formatStateUsing(fn($state) => $state === 'whatsapp' ? 'WhatsApp' : 'Menü'),

                        TextEntry::make('utm_source')
                            ->label('Kaynak (utm_source)')
                            ->badge()
                            ->placeholder('Doğrudan / Organik')
                            ->color('info'),

                        TextEntry::make('utm_campaign')
                            ->label('Kampanya (utm_campaign)')
                            ->placeholder('-')
                            ->weight(FontWeight::SemiBold),

                        TextEntry::make('created_at')
                            ->label('Tarih')
                            ->dateTime('d M Y H:i:s')
                            ->color('gray'),
                    ]),
                ]),

            Section::make('Meta CAPI Parametreleri')
                ->description('Eşleştirme kalitesini belirleyen teknik Meta parametreleri')
                ->schema([
                    Grid::make(2)->schema([

                        TextEntry::make('event_id')
                            ->label('Event ID (Deduplication)')
                            ->copyable()
                            ->fontFamily('mono'),

                        TextEntry::make('fbclid')
                            ->label('Facebook Click ID (fbclid)')
                            ->copyable()
                            ->placeholder('Reklam Tıklaması Yok')
                            ->color(fn($state) => $state ? 'success' : 'gray'),

                        TextEntry::make('fbc')
                            ->label('FBC')
                            ->copyable()
                            ->fontFamily('mono')
                            ->placeholder('-'),

                        TextEntry::make('fbp')
                            ->label('FBP')
                            ->copyable()
                            ->fontFamily('mono')
                            ->placeholder('-'),

                        TextEntry::make('device_id')
                            ->label('Device ID (Hashed)')
                            ->copyable()
                            ->fontFamily('mono')
                            ->columnSpanFull(),

                        TextEntry::make('session_hash')
                            ->label('Session Hash')
                            ->copyable()
                            ->fontFamily('mono')
                            ->columnSpanFull(),
                    ])
                ])
                ->collapsed()
                ->collapsible(),

            Section::make('Ziyaretçi Teknik Verileri')
                ->schema([
                    Grid::make(3)->schema([

                        TextEntry::make('ip_address')
                            ->label('IP Adresi')
                            ->icon('heroicon-m-globe-alt'),

                        TextEntry::make('browser_id')
                            ->label('Tarayıcı Kimliği')
                            ->placeholder('-')
                            ->copyable()
                            ->fontFamily('mono'),

                        TextEntry::make('referer')
                            ->label('Referer')
                            ->placeholder('Yok')
                            ->columnSpanFull(),

                        TextEntry::make('landing_page')
                            ->label('Geldiği Sayfa')
                            ->icon('heroicon-m-link')
                            ->url(fn($state) => $state)
                            ->openUrlInNewTab()
                            ->columnSpanFull(),
                    ])
                ])
                ->collapsed()
                ->collapsible(),

            Section::make('Ek Veri (Payload)')

                ->schema([
                    TextEntry::make('payload')
                        ->label('Payload JSON')
                        ->formatStateUsing(function ($state) {
                            if (blank($state)) {
                                return 'Ek veri yok';
                            }

                            // Array veya object’i güzel JSON string’e çevir
                            return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        })
                        ->copyable()
                        ->extraAttributes([
                            'class' => 'whitespace-pre-wrap text-xs font-mono',
                        ])
                        ->columnSpanFull(),
                ])
                ->collapsed()
                ->collapsible(),


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
                    ->formatStateUsing(
                        fn(string $state) =>
                        $state === 'whatsapp' ? 'WhatsApp' : 'Menü'
                    )
                    ->color(
                        fn(string $state) =>
                        $state === 'whatsapp' ? 'success' : 'warning'
                    ),

                TextColumn::make('utm_source')
                    ->label('Kaynak')
                    ->placeholder('Direct / Organik')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('fbclid')
                    ->label('Reklam')
                    ->formatStateUsing(fn($state) => $state ? 'Meta Ads' : 'Organik')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'gray'),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Dönüşüm Tipi')
                    ->options([
                        'whatsapp' => 'WhatsApp',
                        'menu' => 'Menü',
                    ]),

                Tables\Filters\TernaryFilter::make('fbclid')
                    ->label('Trafik Tipi')
                    ->placeholder('Tümü')
                    ->trueLabel('Reklam (FB Ads)')
                    ->falseLabel('Organik')
                    ->queries(
                        true: fn($query) => $query->whereNotNull('fbclid'),
                        false: fn($query) => $query->whereNull('fbclid'),
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
            ->emptyStateDescription('Meta CAPI veya PWA üzerinden dönüşümler burada görünür.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'view' => Pages\ViewLead::route('/{record}'),
        ];
    }
}

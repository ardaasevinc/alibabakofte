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
    protected static ?string $modelLabel = 'Dönüşüm';
    protected static ?string $pluralModelLabel = 'Dönüşümler (CAPI)';

    public static function form(Form $form): Form
    {
        return $form->schema([]); // Veriler otomatik oluştuğu için form boş kalıyor.
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Dönüşüm Özeti')
                    ->description('Meta CAPI ve PWA üzerinden gelen ana veriler')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('payload.button_id')
                                    ->label('Eylem Tipi')
                                    ->badge()
                                    ->icon(fn($state) => match ($state) {
                                        'meta-menu' => 'heroicon-m-list-bullet',
                                        'meta-whatsapp' => 'heroicon-m-chat-bubble-left-right',
                                        default => 'heroicon-m-finger-print',
                                    })
                                    ->color(fn($state) => match ($state) {
                                        'meta-menu' => 'warning',
                                        'meta-whatsapp' => 'success',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'meta-menu' => 'Menü Görüntüleme',
                                        'meta-whatsapp' => 'WhatsApp İletişim',
                                        default => $state,
                                    }),

                                TextEntry::make('utm_source')
                                    ->label('Kaynak')
                                    ->placeholder('Doğrudan Giriş')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('utm_campaign')
                                    ->label('Kampanya')
                                    ->placeholder('Organik')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('created_at')
                                    ->label('Tarih/Saat')
                                    ->dateTime('d M Y, H:i:s')
                                    ->color('gray'),
                            ]),
                    ]),

                Section::make('Meta İzleme Parametreleri')
                    ->description('Olay eşleştirme kalitesini (Matching Quality) belirleyen teknik veriler')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('event_id')
                                    ->label('Meta Event ID (Deduplication)')
                                    ->copyable()
                                    ->fontFamily('mono')
                                    ->icon('heroicon-m-finger-print'),

                                TextEntry::make('fbclid')
                                    ->label('Facebook Click ID (fbc)')
                                    ->placeholder('Reklam dışı / Organik')
                                    ->copyable()
                                    ->fontFamily('mono')
                                    ->color(fn($state) => $state ? 'success' : 'gray'),

                                TextEntry::make('payload.came_from')
                                    ->label('Dönüşümün Geldiği URL')
                                    ->columnSpanFull()
                                    ->url(fn($state) => $state)
                                    ->openUrlInNewTab()
                                    ->color('primary')
                                    ->icon('heroicon-m-link')
                                    ->placeholder('Bilgi yok'),
                            ]),
                    ])->collapsible(),

                Section::make('Teknik Ziyaretçi Verileri')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('ip_address')->label('IP Adresi')->icon('heroicon-m-globe-alt'),
                                TextEntry::make('user_agent')
                                    ->label('Tarayıcı ve Cihaz Bilgisi')
                                    ->columnSpan(2)
                                    ->size('xs')
                                    ->color('gray'),
                            ]),
                    ])->collapsible()->collapsed(),
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

                TextColumn::make('payload.button_id')
                    ->label('Eylem')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'meta-menu' => 'Menü',
                        'meta-whatsapp' => 'WhatsApp',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'meta-menu' => 'warning',
                        'meta-whatsapp' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('utm_source')
                    ->label('Kaynak')
                    ->placeholder('Direct')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('fbclid')
                    ->label('Reklam')
                    ->formatStateUsing(fn($state) => $state ? '✅' : '❌')
                    ->alignCenter()
                    ->tooltip('Facebook Reklam Tıklaması'),

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
                    ->trueLabel('Ücretli (Meta Ads)')
                    ->falseLabel('Organik / Diğer')
                    ->queries(
                        true: fn($query) => $query->whereNotNull('fbclid'),
                        false: fn($query) => $query->whereNull('fbclid'),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            // TOPLU İŞLEMLER BURADA AKTİF EDİLDİ
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->icon('heroicon-m-trash'),
                ]),
            ])
            ->emptyStateHeading('Henüz veri yok')
            ->emptyStateDescription('Meta CAPI veya PWA üzerinden ilk dönüşüm geldiğinde burada görünecektir.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'view' => Pages\ViewLead::route('/{record}'),
        ];
    }
}
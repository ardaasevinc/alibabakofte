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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Reklam & Başvuru Analizi';
    protected static ?string $modelLabel = 'Başvuru';
    protected static ?string $pluralModelLabel = 'Başvurular';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tarih/Saat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->description(fn($record) => $record->created_at->diffForHumans()),

                TextColumn::make('type')
                    ->label('Dönüşüm Tipi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'whatsapp' => 'success',
                        'menu' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'whatsapp' => 'heroicon-m-chat-bubble-left-right',
                        'menu' => 'heroicon-m-book-open',
                        default => 'heroicon-m-question-mark-circle',
                    }),

                TextColumn::make('utm_source')
                    ->label('Kaynak')
                    ->badge()
                    // direct gelenleri uyarı rengi yaparak dikkat çekiyoruz
                    ->color(fn($state) => in_array($state, ['direct', null]) ? 'danger' : 'info')
                    ->formatStateUsing(fn($state) => strtoupper($state ?? 'DIREKT'))
                    ->searchable(),

                TextColumn::make('utm_campaign')
                    ->label('Kampanya Adı')
                    ->placeholder('Tanımsız / Organik')
                    ->toggleable()
                    ->limit(20),

                // FBCLID varsa bir ikon göstererek reklamdan geldiğini teyit edelim
                TextColumn::make('fbclid')
                    ->label('Meta Reklam')
                    ->formatStateUsing(fn($state) => $state ? '✅ Reklam' : '❌ Değil')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Dönüşüm Kanalı')
                    ->options([
                        'whatsapp' => 'WhatsApp Tıklamaları',
                        'menu' => 'Menü Görüntüleme',
                    ]),

                // Kaynak filtresini daha akıllı hale getirdik
                SelectFilter::make('utm_source')
                    ->label('Reklam Kaynağı')
                    ->options(fn() => Lead::whereNotNull('utm_source')->distinct()->pluck('utm_source', 'utm_source')->toArray())
                    ->searchable(),

                // Tarih filtresine hızlı seçimler eklendi
                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')->label('Başlangıç'),
                        \Filament\Forms\Components\DatePicker::make('created_until')->label('Bitiş'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn($query, $date) => $query->whereDate('created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make('Başlangıç: ' . Carbon::parse($data['created_from'])->toFormattedDateString());
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make('Bitiş: ' . Carbon::parse($data['created_until'])->toFormattedDateString());
                        }
                        return $indicators;
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('İncele'),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Müşteri Dönüşüm Yolculuğu')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('utm_source')
                                    ->label('Geliş Kaynağı')
                                    ->badge()
                                    ->color(fn($state) => $state === 'direct' ? 'danger' : 'info'),
                                TextEntry::make('utm_campaign')->label('Kampanya')->weight('bold'),
                                TextEntry::make('created_at')->label('İşlem Zamanı')->dateTime('d M Y, H:i:s'),
                            ]),
                    ]),
                
                Section::make('Meta CAPI & Teknik Detaylar')
                    ->description('Facebook reklam optimizasyonu verileri.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('event_id')->label('Meta Event ID')->fontFamily('mono')->copyable(),
                                TextEntry::make('fbclid')->label('Facebook Click ID (fbc)')->fontFamily('mono')->placeholder('Reklam dışı giriş'),
                                // Payload içindeki veriyi daha temiz çekelim
                                TextEntry::make('payload.referer')->label('Geldiği URL')->url(fn($state) => $state)->openUrlInNewTab(),
                                TextEntry::make('ip_address')->label('IP Adresi')->copyable(),
                                TextEntry::make('user_agent')->label('Cihaz/Tarayıcı')->size('xs')->columnSpanFull(),
                            ]),
                    ])->collapsible(),
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
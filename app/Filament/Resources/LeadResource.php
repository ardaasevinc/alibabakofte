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
use Illuminate\Database\Eloquent\Builder;

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
                    ->color('info')
                    ->placeholder('Organik/Direkt')
                    ->searchable(),

                TextColumn::make('utm_campaign')
                    ->label('Kampanya Adı')
                    ->placeholder('Tanımsız')
                    ->toggleable(),

                TextColumn::make('ip_address')
                    ->label('IP Adresi')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),
            ])
            ->defaultSort('created_at', 'desc')
            
            // --- FİLTRELER BURADA BAŞLIYOR ---
            ->filters([
                // 1. İşlem Tipine Göre Filtre (WhatsApp / Menü)
                SelectFilter::make('type')
                    ->label('Dönüşüm Kanalı')
                    ->options([
                        'whatsapp' => 'WhatsApp Tıklamaları',
                        'menu' => 'Menü Görüntüleme',
                    ]),

                // 2. UTM Kaynağına Göre (Facebook, Google, Instagram vb.)
                SelectFilter::make('utm_source')
                    ->label('Reklam Kaynağı')
                    ->options(fn () => Lead::distinct()->whereNotNull('utm_source')->pluck('utm_source', 'utm_source')->toArray()),

                // 3. Tarih Aralığı Filtresi (Bugün, Dün, Bu Hafta vb.)
                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')->label('Başlangıç'),
                        \Filament\Forms\Components\DatePicker::make('created_until')->label('Bitiş'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date));
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('İncele'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
                                TextEntry::make('utm_source')->label('Geliş Kaynağı')->badge()->color('info'),
                                TextEntry::make('utm_medium')->label('Medium')->placeholder('cpc / social'),
                                TextEntry::make('utm_campaign')->label('Kampanya')->weight('bold'),
                            ]),
                    ]),
                
                Section::make('Meta CAPI & Teknik Detaylar')
                    ->description('Bu veriler Facebook reklam optimizasyonu için sunucu tarafında işlenir.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('event_id')->label('Meta Event ID')->fontFamily('mono')->copyable(),
                                TextEntry::make('fbclid')->label('Facebook Click ID (fbc)')->fontFamily('mono')->placeholder('Reklam dışı giriş'),
                                TextEntry::make('payload.came_from')->label('Tıklanan Son Sayfa')->url(fn($state) => $state)->openUrlInNewTab()->color('primary'),
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
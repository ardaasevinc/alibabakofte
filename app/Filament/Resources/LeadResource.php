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
    protected static ?string $navigationLabel = 'Dönüşüm Takibi (CAPI)';
    protected static ?string $modelLabel = 'Dönüşüm';
    protected static ?string $pluralModelLabel = 'Dönüşümler';

    public static function form(Form $form): Form
    {
        return $form->schema([]); // Lead manuel oluşturulmaz
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Dönüşüm Özeti')
                    ->description('Meta CAPI, Pixel ve trafik verilerinin birleşik görünümü')
                    ->schema([
                        Grid::make(4)
                            ->schema([

                                TextEntry::make('type')
                                    ->label('Dönüşüm Tipi')
                                    ->badge()
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'whatsapp' => 'WhatsApp',
                                        'menu' => 'Menü',
                                        default => ucfirst($state),
                                    })
                                    ->color(fn($state) => match ($state) {
                                        'whatsapp' => 'success',
                                        'menu' => 'warning',
                                        default => 'gray',
                                    }),

                                TextEntry::make('utm_source')
                                    ->label('Kaynak')
                                    ->badge()
                                    ->placeholder('Direct')
                                    ->color('info'),

                                TextEntry::make('utm_campaign')
                                    ->label('Kampanya')
                                    ->placeholder('-')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('created_at')
                                    ->label('Tarih/Saat')
                                    ->dateTime('d.m.Y H:i:s'),
                            ]),
                    ]),

                Section::make('UTM Verileri')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('utm_medium')->label('Medium'),
                                TextEntry::make('utm_term')->label('Keyword (utm_term)'),
                                TextEntry::make('utm_content')->label('Content (utm_content)'),
                                TextEntry::make('came_from_url')
                                    ->label('Geldiği URL')
                                    ->columnSpan(2)
                                    ->url(fn($state) => $state)
                                    ->openUrlInNewTab()
                                    ->color('primary')
                            ]),
                    ])
                    ->collapsed(),

                Section::make('Meta İzleme Parametreleri (CAPI + Pixel)')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('event_id')
                                    ->label('Event ID (Dedup)')
                                    ->fontFamily('mono')
                                    ->copyable(),

                                TextEntry::make('fbp')
                                    ->label('FBP (Browser ID)')
                                    ->fontFamily('mono')
                                    ->color(fn($state) => $state ? 'success' : 'gray')
                                    ->copyable(),

                                TextEntry::make('fbc')
                                    ->label('FBC (Click ID)')
                                    ->fontFamily('mono')
                                    ->color(fn($state) => $state ? 'warning' : 'gray')
                                    ->copyable(),

                                TextEntry::make('fbclid')
                                    ->label('fbclid')
                                    ->fontFamily('mono')
                                    ->placeholder('-')
                                    ->copyable(),
                            ]),
                    ])
                    ->collapsed(),

                Section::make('Cihaz ve Tarayıcı Verileri')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('platform')->label('Platform'),
                                TextEntry::make('is_mobile')
                                    ->label('Mobil mi?')
                                    ->formatStateUsing(fn($state) => $state ? 'Evet' : 'Hayır')
                                    ->badge()
                                    ->color(fn($state) => $state ? 'success' : 'gray'),

                                TextEntry::make('ip_address')
                                    ->label('IP Adresi')
                                    ->icon('heroicon-m-globe-alt'),

                                TextEntry::make('user_agent')
                                    ->label('User Agent')
                                    ->columnSpanFull()
                                    ->color('gray'),
                            ]),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('created_at')
                    ->label('Zaman')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->description(fn($record) => $record->created_at->diffForHumans()),

                TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'whatsapp' => 'WhatsApp',
                        'menu' => 'Menü',
                        default => ucfirst($state),
                    })
                    ->color(fn($state) => match ($state) {
                        'whatsapp' => 'success',
                        'menu' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('utm_source')
                    ->label('Kaynak')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('utm_campaign')
                    ->label('Kampanya')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('fbclid')
                    ->label('Reklam')
                    ->formatStateUsing(fn($state) => $state ? 'Ads' : 'Organik')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'gray'),

                TextColumn::make('came_from_url')
                    ->label('Geldiği URL')
                    ->url(fn($state) => $state)
                    ->limit(20)
                    ->openUrlInNewTab()
                    ->toggleable(),
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
                    ->label('Trafik Kaynağı')
                    ->placeholder('Hepsi')
                    ->trueLabel('Reklam')
                    ->falseLabel('Organik')
                    ->queries(
                        true: fn($q) => $q->whereNotNull('fbclid'),
                        false: fn($q) => $q->whereNull('fbclid'),
                    ),

                Tables\Filters\Filter::make('today')
                    ->label('Bugün')
                    ->query(fn($q) => $q->whereDate('created_at', today())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->emptyStateHeading('Henüz veri yok')
            ->emptyStateDescription('Meta CAPI üzerinden ilk dönüşüm geldiğinde burada görünür.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'view' => Pages\ViewLead::route('/{record}'),
        ];
    }
}

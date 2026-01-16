<?php


namespace App\Filament\Resources;

use App\Filament\Resources\InstagramPostResource\Pages;
use App\Models\InstagramPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InstagramPostResource extends Resource
{
    protected static ?string $model = InstagramPost::class;
    protected static ?string $navigationIcon = 'heroicon-o-camera';
    protected static ?string $navigationLabel = 'Instagram Paylaşımları';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Paylaşımın önizlemesi
                Tables\Columns\ImageColumn::make('media_url')
                    ->label('Görsel')
                    ->circular(),

                // Açıklama (uzunsa kısaltır)
                Tables\Columns\TextColumn::make('caption')
                    ->label('Açıklama')
                    ->limit(40)
                    ->searchable(),

                // Sitede yayınla butonu (Hızlı Toggle)
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label('Sitede Yayınla'),

                // Paylaşım tarihi
                Tables\Columns\TextColumn::make('posted_at')
                    ->label('Tarih')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                // Instagram'a gitmek için buton
                Tables\Columns\TextColumn::make('permalink')
                    ->label('Link')
                    ->formatStateUsing(fn (string $state): string => 'Görüntüle')
                    ->url(fn (InstagramPost $record): string => $record->permalink)
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('posted_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Yayın Durumu'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstagramPosts::route('/'),
        ];
    }
}
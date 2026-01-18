<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstagramPostResource\Pages;
use App\Models\InstagramPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions;

class InstagramPostResource extends Resource
{
    protected static ?string $model = InstagramPost::class;
    protected static ?string $navigationIcon = 'heroicon-o-camera';
    protected static ?string $navigationLabel = 'Instagram Paylaşımları';
    protected static ?string $navigationGroup = 'İçerikler';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                /* ===========================
                 * ÖNİZLEME (Fotoğraf / Video Thumbnail)
                 * =========================== */
                Tables\Columns\ImageColumn::make('preview')
                    ->label('Görsel')
                    ->getStateUsing(function (InstagramPost $record) {
                        return $record->media_type === 'VIDEO'
                            ? ($record->thumbnail_url ?: $record->media_url)
                            : $record->media_url;
                    })
                    ->square()       // daha şık görünüm
                    ->size(50),

                /* ===========================
                 * Açıklama
                 * =========================== */
                Tables\Columns\TextColumn::make('caption')
                    ->label('Açıklama')
                    ->limit(40)
                    ->wrap()
                    ->searchable(),

                /* ===========================
                 * Yayın Durumu Toggle
                 * =========================== */
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label('Sitede Yayınla')
                    ->default(true),

                /* ===========================
                 * Paylaşım Tarihi
                 * =========================== */
                Tables\Columns\TextColumn::make('posted_at')
                    ->label('Tarih')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                /* ===========================
                 * Instagram Linki
                 * =========================== */
                Tables\Columns\TextColumn::make('permalink')
                    ->label('Instagram')
                    ->formatStateUsing(fn () => 'Göster')
                    ->url(fn (InstagramPost $record) => $record->permalink)
                    ->openUrlInNewTab(),
            ])

            /* ===========================
             * Varsayılan Sıralama
             * =========================== */
            ->defaultSort('posted_at', 'desc')

            /* ===========================
             * FİLTRELER
             * =========================== */
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Yayın Durumu'),
            ])

            /* ===========================
             * SATIR ACTIONS (Video İzle)
             * =========================== */
            ->actions([
                Actions\Action::make('playVideo')
                    ->label('İzle')
                    ->icon('heroicon-o-play-circle')
                    ->visible(fn (InstagramPost $record) => $record->media_type === 'VIDEO')
                    ->modalHeading('Video Oynatıcı')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Kapat')
                    ->modalContent(function (InstagramPost $record) {
                        return view('site.filament.instagram.video-player', [
                            'videoUrl' => $record->media_url,
                        ]);
                    }),
            ])

            /* ===========================
             * TOPLU ACTIONS
             * =========================== */
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstagramPosts::route('/'),
        ];
    }
}

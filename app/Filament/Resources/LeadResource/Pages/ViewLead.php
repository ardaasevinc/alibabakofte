<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Resources\Pages\ViewRecord;

class ViewLead extends ViewRecord
{
    /**
     * Bu sayfanın hangi Resource'a bağlı olduğunu belirtir.
     */
    protected static string $resource = LeadResource::class;

    /**
     * Üst kısımdaki butonları özelleştirmek istersen burayı kullanabilirsin.
     */
    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
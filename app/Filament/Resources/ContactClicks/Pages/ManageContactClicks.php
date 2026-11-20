<?php

namespace App\Filament\Resources\ContactClicks\Pages;

use App\Filament\Resources\ContactClicks\ContactClickResource;
use Filament\Resources\Pages\ManageRecords;

class ManageContactClicks extends ManageRecords
{
    protected static string $resource = ContactClickResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

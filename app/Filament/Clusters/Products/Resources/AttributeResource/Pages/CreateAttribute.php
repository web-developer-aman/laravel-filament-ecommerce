<?php

namespace App\Filament\Clusters\Products\Resources\AttributeResource\Pages;

use App\Filament\Clusters\Products\Resources\AttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAttribute extends CreateRecord
{
    protected static string $resource = AttributeResource::class;
}

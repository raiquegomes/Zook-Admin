<?php

namespace App\Filament\Enterprise\Resources\UserResource\Pages;

use App\Filament\Enterprise\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}

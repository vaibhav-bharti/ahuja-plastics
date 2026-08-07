<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Support\EmployeeCredentials;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected ?string $plainPassword = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->plainPassword = $data['password'] ?? null;

        return $data;
    }

    protected function afterCreate(): void
    {
        if (filled($this->plainPassword) && filled($this->record->email)) {
            EmployeeCredentials::sendReadyNotification($this->record, $this->plainPassword);
        }
    }
}

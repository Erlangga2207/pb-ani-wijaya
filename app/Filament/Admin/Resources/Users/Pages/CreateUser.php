<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $role = $data['role'];
        unset($data['role']);

        abort_unless(in_array($role, auth()->user()->assignableRoles(), true), 403);

        $user = static::getModel()::create($data);
        $user->syncRoles([$role]);

        return $user;
    }
}

<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/** Premier administrateur. Identifiants modifiables via .env (ADMIN_EMAIL / ADMIN_PASSWORD). */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@eneam.test')],
            [
                'name' => 'ADMIN',
                'prenoms' => 'Système',
                'password' => env('ADMIN_PASSWORD', 'ChangeMe@2026'),
                'role' => Role::Admin,
                'filiere_id' => null,
                'is_active' => true,
                'must_change_password' => true,
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's users.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@dar-el-idrissi.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('1234'),
                'is_admin' => true,
            ]
        );
    }
}

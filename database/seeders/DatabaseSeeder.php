<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@studentportal.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'), // password na gagamitin mo
            ]
        );
        $admin->forceFill(['role' => 'admin'])->saveQuietly();

        $this->call(ProductSeeder::class);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Berkibar!123');

        User::updateOrCreate(['email' => 'staf@kelurahan.go.id'], [
            'name' => 'Staf Pelayanan',
            'role' => 'staf',
            'password' => $password,
        ]);

        User::updateOrCreate(['email' => 'lurah@kelurahan.go.id'], [
            'name' => 'Bapak Lurah',
            'role' => 'lurah',
            'password' => $password,
        ]);

        User::updateOrCreate(['email' => 'admin@kelurahan.go.id'], [
            'name' => 'Administrator',
            'role' => 'admin',
            'password' => $password,
        ]);
    }
}
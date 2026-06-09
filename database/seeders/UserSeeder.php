<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Enas User',
            'phone' => '0999999999',
            'email' => 'enas@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'Anas User',
            'phone' => '0999966999',
            'email' => 'anas@test.com',
            'password' => Hash::make('password'),
            'role' => 'user'
        ]);
    }
}

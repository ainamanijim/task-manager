<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Ashiima',
            'email' => 'admin@gmail.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'John',
            'email' => 'john@gmail.com',
            'password' => 'password',
            'role' => 'worker',
        ]);

        User::create([
            'name' => 'Mary',
            'email' => 'mary@gmail.com',
            'password' => 'password',
            'role' => 'worker',
        ]);

        User::create([
            'name' => 'Peter',
            'email' => 'peter@gmail.com',
            'password' => 'password',
            'role' => 'worker',
        ]);
    }
}
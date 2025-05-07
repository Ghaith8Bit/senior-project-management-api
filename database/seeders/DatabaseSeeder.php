<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::create([
            'name' => 'Student',
            'email' => 'std@aiu.com',
            'password' => '123123123',
            'role' => 'student'
        ]);
        \App\Models\User::create([
            'name' => 'Supervisor',
            'email' => 'sup@aiu.com',
            'password' => '123123123',
            'role' => 'supervisor'
        ]);
        \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'adm@aiu.com',
            'password' => '123123123',
            'role' => 'admin'
        ]);
    }
}

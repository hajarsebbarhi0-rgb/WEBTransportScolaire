<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
         User::create([
            'nom' => 'NomAdmin',
            'prenom' => 'PrenomAdmin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('ADMIN@2025'),
            'telephone' => '1234567890',
            'status' => 'actif',
            'role' => 'admin',
        ]);
        
    }
}

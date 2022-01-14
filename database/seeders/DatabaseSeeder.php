<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        // Uzytkownik
        DB::table('users')->insert([
            'name' => 'Adam',
            'email' => 'adamh@gmail.com',
            'password' => Hash::make('ADamHolo12!@#1'),
        ]);
        // Zweryfikowany
        DB::table('users')->insert([
            'name' => 'Adam',
            'email' => 'adamh-zweryfikowany@gmail.com',
            'password' => Hash::make('ADamHolo12!@#1'),
            'last_password_change'=>now()
        ]);
        // Stare haslo uzytkownika
        DB::table('users_password_history')->insert([
            'user_id' => '1',
            'password' => Hash::make('ADamHolo12!@#'),
        ]);
    }
}

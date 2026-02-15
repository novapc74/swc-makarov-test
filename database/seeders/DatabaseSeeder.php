<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('TRUNCATE TABLE users CASCADE');

        User::factory()->create([
            'name' => 'SWC user',
            'email' => 'user@example.com',
            'password' => Hash::make('secret'),
        ]);
    }
}

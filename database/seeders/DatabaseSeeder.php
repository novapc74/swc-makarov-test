<?php

namespace Database\Seeders;

use App\Models\Task;
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
        DB::statement('TRUNCATE TABLE tasks, users CASCADE');

        $user = User::factory()->create([
            'name' => 'SWC user',
            'email' => 'user@example.com',
            'password' => Hash::make('secret'),
        ]);

        Task::factory(10)->create(['user_id' => $user->id]);

        User::factory(5)
            ->hasTasks(3)
            ->create();
    }
}

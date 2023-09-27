<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\TaskStatus;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $statusNames = [
            ['name' => 'новая'],
            ['name' => 'завершена'],
            ['name' => 'выполняется'],
            ['name' => 'в архиве'],
            ];

        foreach ($statusNames as $statusName) {
            TaskStatus::create($statusName);
        }

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}

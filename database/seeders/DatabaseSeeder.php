<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\TaskStatus;
use Illuminate\Database\Eloquent\Factories\Sequence;
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

        \App\Models\User::factory(10)->state(new Sequence(
            ['name' => 'Грозный Иван Васильевич'],
            ['name' => 'Рогов Андрей Александрович'],
            ['name' => 'Перепелица Максим Игоревич'],
            ['name' => 'Калита Артём Петрович'],
            ['name' => 'Жданов Даниил Гакович'],
            ['name' => 'Соколов Родион Иванович'],
            ['name' => 'Майорова Марта Данииловна'],
            ['name' => 'Фролов Филипп Данилович'],
            ['name' => 'Туманова Мария Леонидовна'],
            ['name' => 'Богданов Степан Алексеевич'],
            ))->create();
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}

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

        \App\Models\Task::factory(18)->state(new Sequence(
            ['name' => 'Исправить ошибку в какой-нибудь строке', 'description' => 'Я тут ошибку нашёл, надо бы её исправить и так далее и так далее'],
            ['name' => 'Допилить дизайн главной страницы', 'description' => 'Вёрстка поехала в далёкие края. Нужно удалить бутстрап!'],
            ['name' => 'Отрефакторить авторизацию', 'description' => 'Выпилить всё легаси, которое найдёшь'],
            ['name' => 'Доработать команду подготовки БД', 'description' => 'За одно добавить тестовых данных'],
            ['name' => 'Пофиксить вон ту кнопку', 'description' => 'Кажется она не того цвета'],
            ['name' => 'Исправить поиск', 'description' => 'Не ищет то, что мне хочется'],
            ['name' => 'Добавить интеграцию с облаками', 'description' => 'Они такие мягкие и пушистые'],
            ['name' => 'Выпилить лишние зависимости', 'description' => ''],
            ['name' => 'Запилить сертификаты', 'description' => 'Кому-то же они нужны?'],
            ['name' => 'Выпилить игру престолов', 'description' => 'Этот сериал никому не нравится! :)'],
            ['name' => 'Пофиксить спеку во всех репозиториях', 'description' => 'Передать Олегу, чтобы больше не ронял прод'],
            ['name' => 'Вернуть крошки', 'description' => 'Андрей, это задача для тебя'],
            ['name' => 'Установить Linux', 'description' => 'Не забыть потестировать'],
            ['name' => 'Потребовать прибавки к зарплате', 'description' => 'Кризис это не время, чтобы молчать!'],
            ['name' => 'Добавить поиск по фото', 'description' => 'Только не по моему'],
            ['name' => 'Съесть еще этих прекрасных французских булочек', 'description' => ''],
            ['name' => 'Найти чудо', 'description' => 'Чудо-чудное, диво-дивное.'],
            ['name' => 'Исправить ошибку в самой длинной строке', 'description' => 'Самая длинная строка находится в Тридевятом Царстве'],
            ))->create();

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}

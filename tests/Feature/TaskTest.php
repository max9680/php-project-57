<?php

namespace Tests\Feature;

use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{

    public function testCreatePageExists()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $res = $this->actingAs($user)->get(route('tasks.create'));

        $res->assertStatus(200);

        $res->assertSeeText('Создать задачу');
    }

    public function testStore()
    {
        $this->withoutExceptionHandling();

        $status = TaskStatus::factory()->create();
        $user = User::factory()->create();

        $data = [
            'name' => 'first task',
            'description' => 'many words in description',
            'status_id' => $status->id,
            'assigned_to_id' => null,
            'labels' => [],
        ];

        $res = $this->actingAs($user)->post(route('tasks.store', $data));

        $res->assertRedirectToRoute('tasks.index');

        $this->assertDatabaseCount('tasks', 1);

        $this->assertDatabaseHas('tasks', [
            'name' => $data['name'],
        ]);
    }

    public function testStoreByOnlyAuthUser()
    {
        $status = TaskStatus::factory()->create();
        $user = User::factory()->create();

        $data = [
            'name' => 'first task',
            'description' => 'many words in description',
            'status_id' => $status->id,
            'created_by_id' => $user->id,
            'assigned_to_id' => null,
        ];

        $res = $this->post(route('tasks.store', $data));

        $res->assertRedirectToRoute('login');
    }

    public function testStoreNameRequire()
    {
        $status = TaskStatus::factory()->create();
        $user = User::factory()->create();

        $data = [
            'name' => '',
            'description' => 'many words in description',
            'status_id' => $status->id,
            'created_by_id' => $user->id,
            'assigned_to_id' => null,
        ];

        $res = $this->actingAs($user)->post(route('tasks.store', $data));

        $res->assertSessionHasErrors([
            'name' => 'Это обязательное поле',
        ]);
    }

    public function testIndexPageExists()
    {
        $this->withoutExceptionHandling();

        User::factory(5)->create();

        TaskStatus::factory(5)->create();

        $tasks = Task::factory(10)->create();

        $res = $this->get(route('tasks.index'));

        $res->assertStatus(200);

        $res->assertSeeText('Задачи');

        $res->assertViewIs('task.index');

        $names = $tasks->pluck('name')->toArray();

        $res->assertSeeText($names);
    }

    public function testShowPageExists()
    {
        $this->withoutExceptionHandling();

        User::factory(5)->create();

        TaskStatus::factory(5)->create();

        Task::factory(10)->create();

        $task = Task::get()->random();

        $res = $this->get(route('tasks.show', $task->id));

        $res->assertOk();

        $res->assertViewIs('task.show');

        $res->assertSeeText('Просмотр задачи:');

        $res->assertSeeText($task->name);

        $res->assertSeeText($task->description);
    }

    public function testUpdatePageExists()
    {
        $this->withoutExceptionHandling();

        User::factory(5)->create();

        TaskStatus::factory(5)->create();

        Task::factory(10)->create();

        $user = User::get()->random();

        $task = Task::get()->random();

        $res = $this->actingAs($user)->get(route('tasks.edit', $task->id));

        $res->assertViewIs('task.edit');
    }

    public function testUpdate()
    {
        $this->withoutExceptionHandling();

        User::factory(5)->create();

        TaskStatus::factory(5)->create();

        Task::factory(10)->create();

        $task = Task::get()->random();

        $user = User::get()->random();

        $data = [
            'name' => 'updated Name',
            'description' => 'updatedDescription',
            'status_id' => TaskStatus::get()->random()->id,
            'assigned_to_id' => User::get()->random()->id,
            'labels' => [],
        ];

        $res = $this->actingAs($user)->patch(route('tasks.update', $task->id), $data);

        $res->assertRedirectToRoute('tasks.index');

        $updatedTask = Task::where('id', $task->id)->first();

        $this->assertEquals($updatedTask->id, $task->id);

        $this->assertEquals($updatedTask->name, $data['name']);
        $this->assertEquals($updatedTask->description, $data['description']);

    }

    public function testUpdateByOnlyAuthUser()
    {
        User::factory(5)->create();

        TaskStatus::factory(5)->create();

        Task::factory(10)->create();

        $task = Task::get()->random();

        $data = [
            'name' => 'updated Name',
            'description' => 'updatedDescription',
            'status_id' => TaskStatus::get()->random()->id,
            'assigned_to_id' => User::get()->random()->id,
        ];

        $res = $this->patch(route('tasks.update', $task->id), $data);

        $res->assertRedirectToRoute('login');
    }

    public function testDelete()
    {
        $this->withoutExceptionHandling();

        $status = TaskStatus::factory()->create();
        $user = User::factory()->create();

        $data = [
            'name' => 'first task',
            'description' => 'many words in description',
            'status_id' => $status->id,
            'assigned_to_id' => $user->id,
            'labels' => [],
        ];

        $this->actingAs($user)->post(route('tasks.store', $data));

        $this->assertDatabaseCount('tasks', 1);

        $task = Task::first();

        $res = $this->actingAs($user)->delete(route('tasks.destroy', $task->id));

        $this->assertDatabaseCount('tasks', 0);

        $res->assertRedirectToRoute('tasks.index');
    }

    public function testDeleteByOnlyOwner()
    {
        $status = TaskStatus::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $data1 = [
            'name' => 'first task',
            'description' => 'many words in description',
            'status_id' => $status->id,
            'assigned_to_id' => $user2->id,
            'labels' => [],
        ];

        $data2 = [
            'name' => 'second task',
            'description' => 'many words in description',
            'status_id' => $status->id,
            'assigned_to_id' => null,
            'labels' => [],
        ];

        $this->actingAs($user1)->post(route('tasks.store', $data1));
        $this->actingAs($user2)->post(route('tasks.store', $data2));

        $this->assertDatabaseCount('tasks', 2);

        $task1 = Task::where('name', 'first task')->first();
        $task2 = Task::where('name', 'second task')->first();

        $this->actingAs($user1)->delete(route('tasks.destroy', $task1->id));

        $this->assertDatabaseCount('tasks', 1);

        $this->actingAs($user1)->delete(route('tasks.destroy', $task2->id));

        $this->assertDatabaseCount('tasks', 1);
    }
}

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
    use RefreshDatabase;

    /** @test */
    public function testCreate_page_exists()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $res = $this->actingAs($user)->get('/tasks/create');

        $res->assertStatus(200);

        $res->assertSeeText('Создать задачу');
    }

    /** @test */
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

        $res = $this->actingAs($user)->post('/tasks', $data);

        $res->assertRedirectToRoute('tasks.index');

        $this->assertDatabaseCount('tasks', 1);

        $task = Task::first();

        $this->assertEquals($data['name'], $task->name);
    }

    /** @test */
    public function testStore_by_only_auth_user()
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

        $res = $this->post('/tasks', $data);

        $res->assertRedirectToRoute('login');
    }

    /** @test */
    public function testStore_name_require()
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

        $res = $this->actingAs($user)->post('/tasks', $data);

        $res->assertSessionHasErrors([
            'name' => 'Это обязательное поле',
        ]);
    }

    /** @test */
    public function testIndex_page_exists()
    {
        $this->withoutExceptionHandling();

        User::factory(5)->create();

        TaskStatus::factory(5)->create();

        $tasks = Task::factory(10)->create();

        $res = $this->get('/tasks');

        $res->assertStatus(200);

        $res->assertSeeText('Задачи');

        $res->assertViewIs('task.index');

        $names = $tasks->pluck('name')->toArray();

        $res->assertSeeText($names);
    }

    /** @test */
    public function testShow_page_exists()
    {
        $this->withoutExceptionHandling();

        User::factory(5)->create();

        TaskStatus::factory(5)->create();

        Task::factory(10)->create();

        $task = Task::get()->random();

        $res = $this->get('/tasks/' . $task->id);

        $res->assertOk();

        $res->assertViewIs('task.show');

        $res->assertSeeText('Просмотр задачи:');

        $res->assertSeeText($task->name);

        $res->assertSeeText($task->description);
    }

    /** @test */
    public function testUpdate_page_exists()
    {
        $this->withoutExceptionHandling();

        User::factory(5)->create();

        TaskStatus::factory(5)->create();

        Task::factory(10)->create();

        $user = User::get()->random();

        $task = Task::get()->random();

        $res = $this->actingAs($user)->get('/tasks/' . $task->id . '/edit');

        $res->assertViewIs('task.edit');
    }

    /** @test */
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

        $res = $this->actingAs($user)->patch('/tasks/' . $task->id, $data );

        $res->assertRedirectToRoute('tasks.index');

        $updatedTask = Task::where('id', $task->id)->first();

        $this->assertEquals($updatedTask->id, $task->id);

        $this->assertEquals($updatedTask->name, $data['name']);
        $this->assertEquals($updatedTask->description, $data['description']);

//        dd($updatedTask);
    }

    /** @test */
    public function testUpdate_by_only_auth_user()
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

        $res = $this->patch('/tasks/' . $task->id, $data );

        $res->assertRedirectToRoute('login');
    }

    /** @test */
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

        $this->actingAs($user)->post('/tasks', $data);

        $this->assertDatabaseCount('tasks', 1);

        $task = Task::first();

        $res = $this->actingAs($user)->delete('/tasks/' . $task->id);

        $this->assertDatabaseCount('tasks', 0);

        $res->assertRedirectToRoute('tasks.index');
    }

    /** @test */
    public function testDelete_by_only_owner()
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

        $this->actingAs($user1)->post('/tasks', $data1);
        $this->actingAs($user2)->post('/tasks', $data2);

        $this->assertDatabaseCount('tasks', 2);

        $task1 = Task::where('name', 'first task')->first();
        $task2 = Task::where('name', 'second task')->first();

        $this->actingAs($user1)->delete('/tasks/' . $task1->id);

        $this->assertDatabaseCount('tasks', 1);

        $this->actingAs($user1)->delete('/tasks/' . $task2->id);

        $this->assertDatabaseCount('tasks', 1);
    }
}

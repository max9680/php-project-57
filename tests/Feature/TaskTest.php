<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Tests\TestCase;

class TaskTest extends TestCase
{
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        User::factory(3)->create();

        TaskStatus::factory(3)->create();

        Task::factory(3)->create();
    }

    public function testCreatePageExists()
    {
        $this->withoutExceptionHandling();

        $res = $this->actingAs($this->user)->get(route('tasks.create'));

        $res->assertStatus(200);

        $res->assertSeeText('Создать задачу');
    }

    public function testStore()
    {
        $this->withoutExceptionHandling();

        $data = Task::factory()->make()->toArray();

        $res = $this->actingAs($this->user)->post(route('tasks.store', $data));

        $res->assertRedirectToRoute('tasks.index');

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('tasks', 4);

        $this->assertDatabaseHas('tasks', [
            'name' => $data['name'],
        ]);
    }

    public function testStoreByOnlyAuthUser()
    {
        $data = Task::factory()->make()->toArray();

        $res = $this->post(route('tasks.store', $data));

        $res->assertForbidden();
    }

    public function testStoreNameRequire()
    {
        $data = Task::factory()->make()->toArray();

        $data['name'] = '';

        $res = $this->actingAs($this->user)->post(route('tasks.store', $data));

        $res->assertSessionHasErrors([
            'name' => 'Это обязательное поле',
        ]);
    }

    public function testIndexPageExists()
    {
        $this->withoutExceptionHandling();

        $res = $this->get(route('tasks.index'));

        $res->assertStatus(200);

        $res->assertSeeText('Задачи');

        $res->assertViewIs('task.index');

        $tasks = Task::all();

        $names = $tasks->pluck('name')->toArray();

        $res->assertSeeText($names);
    }

    public function testShowPageExists()
    {
        $this->withoutExceptionHandling();

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

        $user = User::get()->random();

        $task = Task::get()->random();

        $res = $this->actingAs($user)->get(route('tasks.edit', $task->id));

        $res->assertViewIs('task.edit');
    }

    public function testUpdate()
    {
        $this->withoutExceptionHandling();

        $task = Task::get()->random();

        $user = User::get()->random();

        $data = Task::factory()->make()->toArray();

        $res = $this->actingAs($user)->patch(route('tasks.update', $task->id), $data);

        $res->assertRedirectToRoute('tasks.index');

        $res->assertSessionHasNoErrors();

        $updatedTask = Task::where('id', $task->id)->first();

        $this->assertEquals(optional($updatedTask)->id, $task->id);

        $this->assertEquals(optional($updatedTask)->name, $data['name']);
        $this->assertEquals(optional($updatedTask)->description, $data['description']);
    }

    public function testUpdateByOnlyAuthUser()
    {
        $task = Task::get()->random();

        $data = Task::factory()->make()->toArray();

        $res = $this->patch(route('tasks.update', $task->id), $data);

        $res->assertForbidden();
    }

    public function testDelete()
    {
        $this->withoutExceptionHandling();

        $data = Task::factory()->make()->toArray();
        $data['created_by_id'] = $this->user->id;

        $this->assertDatabaseCount('tasks', 3);

        Task::create($data);

        $this->assertDatabaseCount('tasks', 4);

        $task = Task::where('created_by_id', $this->user->id)->first();

        $res = $this->actingAs($this->user)->delete(route('tasks.destroy', optional($task)->id));

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('tasks', 3);

        $res->assertRedirectToRoute('tasks.index');
    }

    public function testDeleteByOnlyOwner()
    {
        $user1 = User::where('id', 1)->first();
        $user2 = User::where('id', 2)->first();

        $data1 = Task::factory()->make()->toArray();
        $data1['created_by_id'] = $user1->id;

        $data2 = Task::factory()->make()->toArray();
        $data2['created_by_id'] = $user2->id;

        Task::create($data1);
        Task::create($data2);

        $this->assertDatabaseCount('tasks', 5);

        $task1 = Task::where('created_by_id', $user1->id)->first();
        $task2 = Task::where('created_by_id', $user2->id)->first();

        $res = $this->actingAs($user1)->delete(route('tasks.destroy', $task1->id));

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('tasks', 4);

        $res = $this->actingAs($user1)->delete(route('tasks.destroy', $task2->id));

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('tasks', 4);
    }
}

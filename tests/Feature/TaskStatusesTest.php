<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Tests\TestCase;

class TaskStatusesTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        TaskStatus::factory(3)->create();
    }

    public function testCreatePageExists()
    {
        $this->withoutExceptionHandling();

        $res = $this->actingAs($this->user)->get(route('task_statuses.create'));

        $res->assertStatus(200);
    }

    public function testStore()
    {
        $this->withoutExceptionHandling();

        $data = TaskStatus::factory()->make()->toArray();

        $res = $this->actingAs($this->user)->post(route('task_statuses.store', $data));

        $res->assertRedirectToRoute('task_statuses.index');

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseHas('task_statuses', [
            'name' => $data['name'],
        ]);
    }

    public function testStoreNameRequire()
    {
        $data = [
            'name' => '',
        ];

        $res = $this->actingAs($this->user)->post(route('task_statuses.store', $data));

        $res->assertSessionHasErrors([
            'name' => 'Это обязательное поле'
        ]);
    }

    public function testUpdatePageExists()
    {
        $this->withoutExceptionHandling();

        $taskStatus = TaskStatus::all()->first();

        $res = $this->actingAs($this->user)->get(route('task_statuses.edit', $taskStatus->id));

        $res->assertStatus(200);
    }

    public function testUpdate()
    {
        $this->withoutExceptionHandling();

        $taskStatus = TaskStatus::all()->first();

        $data = TaskStatus::factory()->make()->toArray();

        $res = $this->actingAs($this->user)->patch(route('task_statuses.update', $taskStatus->id), $data);

        $res->assertRedirectToRoute('task_statuses.index');

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseHas('task_statuses', [
            'name' => $data['name'],
            'id' => $taskStatus->id,
        ]);
    }

    public function testUpdateByOnlyAuthUser()
    {
        $data = TaskStatus::factory()->make()->toArray();

        $taskStatus = TaskStatus::all()->first();

        $res = $this->patch(route('task_statuses.update', $taskStatus->id), $data);

        $res->assertRedirectToRoute('login');

        $updatedTaskStatus = TaskStatus::where('id', $taskStatus->id)->first();

        $this->assertNotEquals($updatedTaskStatus->name, $data['name']);

        $this->assertEquals($taskStatus->id, $updatedTaskStatus->id);
    }

    public function testIndex()
    {
        $this->withoutExceptionHandling();

        $res = $this->get(route('task_statuses.index'));

        $res->assertViewIs('taskStatus.index');

        $res->assertSeeText('Статусы');

        $taskStatuses = TaskStatus::all();

        $names = $taskStatuses->pluck('name')->toArray();

        $res->assertSeeText($names);
    }

    public function testDelete()
    {
        $this->withoutExceptionHandling();

        $this->assertDatabaseCount('task_statuses', 3);

        $taskStatus = TaskStatus::all()->first();

        $res = $this->actingAs($this->user)->delete(route('task_statuses.destroy', $taskStatus->id));

        $res->assertRedirectToRoute('task_statuses.index');

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('task_statuses', 2);
    }

    public function testDeleteByOnlyAuthUser()
    {
        $this->assertDatabaseCount('task_statuses', 3);

        $taskStatus = TaskStatus::all()->first();

        $res = $this->delete(route('task_statuses.destroy', $taskStatus->id));

        $res->assertRedirectToRoute('login');

        $this->assertDatabaseCount('task_statuses', 3);

        $res = $this->actingAs($this->user)->delete(route('task_statuses.destroy', $taskStatus->id));

        $res->assertRedirectToRoute('task_statuses.index');

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('task_statuses', 2);
    }

    public function testDeleteWhenLinkExists()
    {
        $taskStatus1 = TaskStatus::where('id', 1)->first();
        $taskStatus2 = TaskStatus::where('id', 2)->first();

        $data = Task::factory()->make()->toArray();

        $data['status_id'] = $taskStatus1->id;

        $task = Task::create($data);

        $this->assertDatabaseCount('task_statuses', 3);

        $res = $this->actingAs($this->user)->delete(route('task_statuses.destroy', $taskStatus1->id));

        $this->assertDatabaseCount('task_statuses', 3);

        $res->assertSessionHasNoErrors();

        $task->status_id = $taskStatus2->id;
        $task->save();

        $res = $this->actingAs($this->user)->delete(route('task_statuses.destroy', $taskStatus1->id));

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('task_statuses', 2);
    }
}

<?php

namespace Tests\Feature;

use App\Http\Requests\TaskStatus\StoreRequest;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskStatusesTest extends TestCase
{

    public function testCreatePageExists()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $res = $this->actingAs($user)->get(route('task_statuses.create'));

        $res->assertStatus(200);
    }

    public function testStore()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $data = [
            'name' => 'test name',
        ];

        $res = $this->actingAs($user)->post(route('task_statuses.store', $data));

        $res->assertRedirectToRoute('task_statuses.index');

        $this->assertDatabaseHas('task_statuses', [
            'name' => $data['name'],
        ]);
    }

    public function testStoreNameRequire()
    {
        $user = User::factory()->create();

        $data = [
            'name' => '',
        ];

        $res = $this->actingAs($user)->post(route('task_statuses.store', $data));

        $res->assertSessionHasErrors([
            'name' => 'Это обязательное поле'
        ]);

    }

    public function testUpdatePageExists ()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $taskStatus = TaskStatus::factory()->create();

        $res = $this->actingAs($user)->get(route('task_statuses.edit', $taskStatus->id));

        $res->assertStatus(200);
    }

    public function testUpdate()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $taskStatus = TaskStatus::factory()->create();

        $data = [
            'name' => 'updated',
        ];

        $res = $this->actingAs($user)->patch(route('task_statuses.update', $taskStatus->id), $data);

        $res->assertRedirectToRoute('task_statuses.index');

        $this->assertDatabaseHas('task_statuses', [
            'name' => $data['name'],
            'id' => $taskStatus->id,
        ]);
    }

    public function testUpdateByOnlyAuthUser()
    {
        $taskStatus = TaskStatus::factory()->create();

        $data = [
            'name' => 'updated',
        ];

        $res = $this->patch(route('task_statuses.update', $taskStatus->id), $data);

        $res->assertRedirectToRoute('login');

        $updatedTaskStatus = TaskStatus::first();

        $this->assertNotEquals($updatedTaskStatus->name, $data['name']);

        $this->assertEquals($taskStatus->id, $updatedTaskStatus->id);

    }

    public function testIndex()
    {
        $this->withoutExceptionHandling();

        $taskStatuses = TaskStatus::factory(10)->create();

        $res = $this->get(route('task_statuses.index'));

        $res->assertViewIs('taskStatus.index');

        $res->assertSeeText('Статусы');

        $names = $taskStatuses->pluck('name')->toArray();

        $res->assertSeeText($names);
    }

    public function testDelete()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        TaskStatus::factory(10)->create();

        $this->assertDatabaseCount('task_statuses', 10);

        $taskStatus = TaskStatus::where('id', 1)->first();

        $res = $this->actingAs($user)->delete(route('task_statuses.destroy', $taskStatus->id));

        $this->assertDatabaseCount('task_statuses', 9);

        $res->assertRedirectToRoute('task_statuses.index');
    }

    public function testDeleteByOnlyAuthUser()
    {
        TaskStatus::factory(10)->create();

        $this->assertDatabaseCount('task_statuses', 10);

        $taskStatus = TaskStatus::where('id', 1)->first();

        $res = $this->delete(route('task_statuses.destroy', $taskStatus->id));

        $res->assertRedirectToRoute('login');

        $this->assertDatabaseCount('task_statuses', 10);
    }
}

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

    /** @test */
    public function testCreate_page_exists()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $res = $this->actingAs($user)->get(route('task_statuses.create'));

        $res->assertStatus(200);
    }


    /** @test */
    public function testStore()
    {
        $this->withoutExceptionHandling();

        $data = [
            'name' => 'test name',
        ];

        $res = $this->post(route('task_statuses.store', $data));

        $res->assertRedirectToRoute('task_statuses.index');

        $this->assertDatabaseHas('task_statuses', [
            'name' => $data['name'],
        ]);
    }

    /** @test */
    public function testStore_name_require()
    {
        $data = [
            'name' => '',
        ];

        $res = $this->post(route('task_statuses.store', $data));

        $res->assertSessionHasErrors([
            'name' => 'Это обязательное поле'
        ]);

    }

    /** @test */
    public function testUpdate_page_exists ()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $taskStatus = TaskStatus::factory()->create();

        $res = $this->actingAs($user)->get(route('task_statuses.edit', $taskStatus->id));

        $res->assertStatus(200);
    }

    /** @test */
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

    /** @test */
    public function testUpdate_by_only_auth_user()
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

    /** @test */
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

    /** @test */
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

    /** @test */
    public function testDelete_by_only_auth_user()
    {
        TaskStatus::factory(10)->create();

        $this->assertDatabaseCount('task_statuses', 10);

        $taskStatus = TaskStatus::where('id', 1)->first();

        $res = $this->delete(route('task_statuses.destroy', $taskStatus->id));

        $res->assertRedirectToRoute('login');

        $this->assertDatabaseCount('task_statuses', 10);
    }
}

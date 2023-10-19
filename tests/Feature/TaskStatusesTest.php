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
    use RefreshDatabase;

    /** @test */
    public function testCreate_page_exists()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $res = $this->actingAs($user)->get('/task_statuses/create');

        $res->assertStatus(200);
    }


    /** @test */
    public function testStore()
    {
        $this->withoutExceptionHandling();

        $data = [
            'name' => 'test name',
        ];

        $res = $this->post('/task_statuses', $data);

        $res->assertRedirectToRoute('task_statuses.index');

        $taskStatus = TaskStatus::first();

        $this->assertEquals($data['name'], $taskStatus->name);
    }

    /** @test */
    public function testStore_name_require()
    {
        $data = [
            'name' => '',
        ];

        $res = $this->post('/task_statuses', $data);

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

        $res = $this->actingAs($user)->get('/task_statuses/' . $taskStatus->id . '/edit');

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

        $res = $this->actingAs($user)->patch('/task_statuses/' . $taskStatus->id, $data);

        $res->assertRedirectToRoute('task_statuses.index');

        $updatedTaskStatus = TaskStatus::first();

        $this->assertEquals($updatedTaskStatus->name, $data['name']);

        $this->assertEquals($taskStatus->id, $updatedTaskStatus->id);

    }

    /** @test */
    public function testUpdate_by_only_auth_user()
    {
        $taskStatus = TaskStatus::factory()->create();

        $data = [
            'name' => 'updated',
        ];

        $res = $this->patch('/task_statuses/' . $taskStatus->id, $data);

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

        $res = $this->get('/task_statuses');

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

        $res = $this->actingAs($user)->delete('/task_statuses/' . $taskStatus->id);

        $this->assertDatabaseCount('task_statuses', 9);

        $res->assertRedirectToRoute('task_statuses.index');
    }

    /** @test */
    public function testDelete_by_only_auth_user()
    {
        TaskStatus::factory(10)->create();

        $this->assertDatabaseCount('task_statuses', 10);

        $taskStatus = TaskStatus::where('id', 1)->first();

        $res = $this->delete('/task_statuses/' . $taskStatus->id);

        $res->assertRedirectToRoute('login');

        $this->assertDatabaseCount('task_statuses', 10);
    }
}

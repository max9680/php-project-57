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
    public function page_for_creating_task_statuses_exists()
    {
        $this->withoutExceptionHandling();

        $res = $this->get('/task_statuses/create');

        $res->assertStatus(200);
    }


    /** @test */
    public function a_task_status_can_be_stored()
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
    public function atttibute_name_is_required_for_task_status()
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
    public function page_for_update_the_task_status_exists ()
    {
        $this->withoutExceptionHandling();

        $taskStatus = TaskStatus::factory()->create();

        $res = $this->get('/task_statuses/' . $taskStatus->id);

        $res->assertStatus(200);
    }

    /** @test */
    public function a_task_status_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $taskStatus = TaskStatus::factory()->create();

        $data = [
            'name' => 'updated',
        ];

        $res = $this->patch('/task_statuses/' . $taskStatus->id, $data);

        $res->assertRedirectToRoute('task_statuses.index');

        $updatedTaskStatus = TaskStatus::first();

        $this->assertEquals($updatedTaskStatus->name, $data['name']);

        $this->assertEquals($taskStatus->id, $updatedTaskStatus->id);

    }

    /** @test */
    public function response_for_route_task_status_index_is_view_task_status_index()
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
    public function task_status_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        TaskStatus::factory(10)->create();

        $this->assertDatabaseCount('task_statuses', 10);

        $taskStatus = TaskStatus::where('id', 1)->first();

        $res = $this->delete('/task_statuses/' . $taskStatus->id);

        $this->assertDatabaseCount('task_statuses', 9);

        $res->assertRedirectToRoute('task_statuses.index');
    }
}

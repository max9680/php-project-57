<?php

namespace Tests\Feature;

use App\Http\Requests\TaskStatus\StoreRequest;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskStatusesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_task_status_can_be_stored()
    {
        $this->withoutExceptionHandling();

        $res = $this->get('/task_statuses/create');
        $res->assertStatus(200);

        $data = [
            'name' => 'test name',
        ];

        $res = $this->post('/task_statuses', $data);

        $res->assertOk();

        $res->assertRedirectToRoute('task_statuses.index');

        $this->assertContains('');

        $taskStatus = TaskStatus::first();

        $this->assertEquals($data['name'], $taskStatus->name);
    }

    public function a_task_can_not_be_created_with_empty_name()
    {
        $this->withoutExceptionHandling();

        $data = [
            'name' => '',
        ];

        $res = $this->post('/task_statuses', $data);

        $res->assertRedirectToRoute('task_statuses.create');
    }
}

<?php

namespace Tests\Feature;

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
    public function page_for_creating_task_exist()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $res = $this->actingAs($user)->get('/task/create');

        $res->assertStatus(200);

        $res->assertSeeText('Создать задачу');
    }

    /** @test */
    public function a_task_can_be_stored_by_auth_user()
    {
        $this->withoutExceptionHandling();

        $status = TaskStatus::factory()->create();
        $user = User::factory()->create();

        $data = [
            'name' => 'first task',
            'description' => 'many words in description',
            'status_id' => $status->id,
//            'created_by_id' => $user->id,
            'assigned_to_id' => null,
        ];

        $res = $this->actingAs($user)->post('/task', $data);

        $res->assertRedirectToRoute('task.index');

        $this->assertDatabaseCount('tasks', 1);

        $task = Task::first();

        $this->assertEquals($data['name'], $task->name);
    }

    /** @test */
    public function a_task_can_be_stored_by_only_auth_user()
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

        $res = $this->post('/task', $data);

        $res->assertRedirectToRoute('login');
    }

    /** @test */
    public function attribut_name_required_for_task()
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

        $res = $this->actingAs($user)->post('/task', $data);

        $res->assertSessionHasErrors([
            'name' => 'Это обязательное поле',
        ]);
    }
}

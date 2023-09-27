<?php

namespace Tests\Feature;

use App\Models\TaskStatus;
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

        $res = $this->get('/task/create');

        $res->assertStatus(200);

        $res->assertSeeText('Создать задачу');
    }

    /** @test */
    public function a_task_can_be_stored()
    {
        $this->withoutExceptionHandling();

        $status = TaskStatus::factory()->create();

        $data = [
            'name' => 'first task',
            'description' => 'many words in description',
            'status_id' => $status->id,
        ];
    }
}

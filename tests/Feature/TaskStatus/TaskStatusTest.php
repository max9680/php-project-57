<?php

namespace Tests\Feature\TaskStatus;

use App\Models\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskStatusTest extends TestCase
{
    use RefreshDatabase;

    public function testStore(): void
    {
        $body = TaskStatus::factory()->make()->toArray();

        $response = $this->postJson(route('api.task_statuses.store'), $body);

        $response->assertCreated();

        $this->assertDatabaseHas('task_statuses', $body);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->taskStatus = TaskStatus::factory()->count(2)->create();
    }
}

<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;

class TaskStatusesTest extends TestCase
{
    protected User $user;
    protected Collection $taskStatuses;
    protected const INITIAL_QUANTITY_TASKSTATUSES_IN_DB = 3;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->taskStatuses = TaskStatus::factory(self::INITIAL_QUANTITY_TASKSTATUSES_IN_DB)->create();
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
            'name' => __('validation.required'),
        ]);
    }

    public function testEditPageExists()
    {
        $this->withoutExceptionHandling();

        $taskStatus = $this->taskStatuses->first();

        $res = $this->actingAs($this->user)->get(route('task_statuses.edit', optional($taskStatus)->id));

        $res->assertStatus(200);
    }

    public function testUpdate()
    {
        $this->withoutExceptionHandling();

        $taskStatus = $this->taskStatuses->first();

        $data = TaskStatus::factory()->make()->toArray();

        $res = $this->actingAs($this->user)->patch(route('task_statuses.update', optional($taskStatus)->id), $data);

        $res->assertRedirectToRoute('task_statuses.index');

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseHas('task_statuses', [
            'name' => $data['name'],
            'id' => optional($taskStatus)->id,
        ]);
    }

    public function testUpdateByOnlyAuthUser()
    {
        $data = TaskStatus::factory()->make()->toArray();

        $taskStatus = $this->taskStatuses->first();

        $res = $this->patch(route('task_statuses.update', optional($taskStatus)->id), $data);

        $res->assertForbidden();

        $updatedTaskStatus = TaskStatus::where('id', optional($taskStatus)->id)->first();

        $this->assertNotEquals(optional($updatedTaskStatus)->name, $data['name']);

        $this->assertEquals(optional($taskStatus)->id, optional($updatedTaskStatus)->id);
    }

    public function testIndex()
    {
        $this->withoutExceptionHandling();

        $res = $this->get(route('task_statuses.index'));

        $res->assertViewIs('taskStatus.index');

        $res->assertSeeText(__('strings.statuses'));

        $taskStatuses = TaskStatus::all();

        $names = $taskStatuses->pluck('name')->toArray();

        $res->assertSeeText($names);
    }

    public function testDelete()
    {
        $this->withoutExceptionHandling();

        $this->assertDatabaseCount('task_statuses', self::INITIAL_QUANTITY_TASKSTATUSES_IN_DB);

        $taskStatus = $this->taskStatuses->first();

        $res = $this->actingAs($this->user)->delete(route('task_statuses.destroy', optional($taskStatus)->id));

        $res->assertRedirectToRoute('task_statuses.index');

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('task_statuses', self::INITIAL_QUANTITY_TASKSTATUSES_IN_DB - 1);
    }

    public function testDeleteByOnlyAuthUser()
    {
        $this->assertDatabaseCount('task_statuses', self::INITIAL_QUANTITY_TASKSTATUSES_IN_DB);

        $taskStatus = $this->taskStatuses->first();

        $res = $this->delete(route('task_statuses.destroy', optional($taskStatus)->id));

        $res->assertForbidden();

        $this->assertDatabaseCount('task_statuses', self::INITIAL_QUANTITY_TASKSTATUSES_IN_DB);
    }

    public function testDeleteWhenLinkExists()
    {
        $taskStatus = $this->taskStatuses->first();

        $data = Task::factory()->make()->toArray();

        $data['status_id'] = optional($taskStatus)->id;

        Task::create($data);

        $this->assertDatabaseCount('task_statuses', self::INITIAL_QUANTITY_TASKSTATUSES_IN_DB);

        $res = $this->actingAs($this->user)->delete(route('task_statuses.destroy', optional($taskStatus)->id));

        $res->assertSessionHas('laravel_flash_message', [
            'message' => __('messages.status.deleted.error'),
            'class' => 'failure',
            'level' => null
        ]);

        $this->assertDatabaseCount('task_statuses', self::INITIAL_QUANTITY_TASKSTATUSES_IN_DB);
    }
}

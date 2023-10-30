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
            'name' => 'Это обязательное поле'
        ]);
    }

    public function testEditPageExists()
    {
        $this->withoutExceptionHandling();

        $taskStatus = $this->taskStatuses->first();

        $res = $this->actingAs($this->user)->get(route('task_statuses.edit', optional($taskStatus)->id));
//        $res = $this->actingAs($this->user)->get(route('task_statuses.edit', $this->taskStatuses->first()->id));

        $res->assertStatus(200);
    }

    public function testUpdate()
    {
        $this->withoutExceptionHandling();

        $taskStatus = TaskStatus::where('id', 1)->first();

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

        $taskStatus = TaskStatus::where('id', 1)->first();

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

        $res->assertSeeText('Статусы');

        $taskStatuses = TaskStatus::all();

        $names = $taskStatuses->pluck('name')->toArray();

        $res->assertSeeText($names);
    }

    public function testDelete()
    {
        $this->withoutExceptionHandling();

        $this->assertDatabaseCount('task_statuses', self::INITIAL_QUANTITY_TASKSTATUSES_IN_DB);

        $taskStatus = TaskStatus::where('id', 1)->first();

        $res = $this->actingAs($this->user)->delete(route('task_statuses.destroy', optional($taskStatus)->id));

        $res->assertRedirectToRoute('task_statuses.index');

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('task_statuses', self::INITIAL_QUANTITY_TASKSTATUSES_IN_DB - 1);
    }

    public function testDeleteByOnlyAuthUser()
    {
        $this->assertDatabaseCount('task_statuses', self::INITIAL_QUANTITY_TASKSTATUSES_IN_DB);

        $taskStatus = TaskStatus::where('id', 1)->first();

        $res = $this->delete(route('task_statuses.destroy', optional($taskStatus)->id));

        $res->assertForbidden();

        $this->assertDatabaseCount('task_statuses', self::INITIAL_QUANTITY_TASKSTATUSES_IN_DB);

        $res = $this->actingAs($this->user)->delete(route('task_statuses.destroy', optional($taskStatus)->id));

        $res->assertRedirectToRoute('task_statuses.index');

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('task_statuses', self::INITIAL_QUANTITY_TASKSTATUSES_IN_DB - 1);
    }

    public function testDeleteWhenLinkExists()
    {
        $taskStatus1 = TaskStatus::where('id', 1)->first();
        $taskStatus2 = TaskStatus::where('id', 2)->first();

        $data = Task::factory()->make()->toArray();

        $data['status_id'] = optional($taskStatus1)->id;

        $task = Task::create($data);

        $this->assertDatabaseCount('task_statuses', self::INITIAL_QUANTITY_TASKSTATUSES_IN_DB);

        $res = $this->actingAs($this->user)->delete(route('task_statuses.destroy', optional($taskStatus1)->id));

        $this->assertDatabaseCount('task_statuses', self::INITIAL_QUANTITY_TASKSTATUSES_IN_DB);

        $res->assertSessionHasNoErrors();

        $task->status_id = optional($taskStatus2)->id;
        $task->save();

        $res = $this->actingAs($this->user)->delete(route('task_statuses.destroy', optional($taskStatus1)->id));

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('task_statuses', self::INITIAL_QUANTITY_TASKSTATUSES_IN_DB - 1);
    }
}

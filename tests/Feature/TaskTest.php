<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;

class TaskTest extends TestCase
{
    protected User $user;
    protected Collection $tasks;
    protected Collection $statuses;
    protected const INITIAL_QUANTITY_USERS_IN_DB = 3;
    protected const INITIAL_QUANTITY_TASKSTATUSES_IN_DB = 3;
    protected const INITIAL_QUANTITY_TASKS_IN_DB = 3;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        User::factory(self::INITIAL_QUANTITY_USERS_IN_DB)->create();

        $this->statuses = TaskStatus::factory(self::INITIAL_QUANTITY_TASKSTATUSES_IN_DB)->create();

        $this->tasks = Task::factory(self::INITIAL_QUANTITY_TASKS_IN_DB)->create();
    }

    public function testCreatePageExists()
    {
        $this->withoutExceptionHandling();

        $res = $this->actingAs($this->user)->get(route('tasks.create'));

        $res->assertStatus(200);

        $res->assertSeeText(__('strings.create task'));
    }

    public function testStore()
    {
        $this->withoutExceptionHandling();

        $data = Task::factory()->make()->toArray();

        $res = $this->actingAs($this->user)->post(route('tasks.store', $data));

        $res->assertRedirectToRoute('tasks.index');

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('tasks', self::INITIAL_QUANTITY_TASKS_IN_DB + 1);

        $this->assertDatabaseHas('tasks', [
            'name' => $data['name'],
        ]);
    }

    public function testStoreByOnlyAuthUser()
    {
        $data = Task::factory()->make()->toArray();

        $res = $this->post(route('tasks.store', $data));

        $res->assertForbidden();
    }

    public function testStoreNameRequire()
    {
        $data = Task::factory()->make()->toArray();

        $data['name'] = '';

        $res = $this->actingAs($this->user)->post(route('tasks.store', $data));

        $res->assertSessionHasErrors([
            'name' => 'Это обязательное поле',
        ]);
    }

    public function testIndexPageExists()
    {
        $this->withoutExceptionHandling();

        $res = $this->get(route('tasks.index'));

        $res->assertStatus(200);

        $res->assertViewIs('task.index');

        $tasks = Task::all();

        $names = $tasks->pluck('name')->toArray();

        $res->assertSeeText($names);
    }

    public function testShowPageExists()
    {
        $this->withoutExceptionHandling();

        $task = $this->tasks->first();

        $res = $this->get(route('tasks.show', optional($task)->id));

        $res->assertOk();

        $res->assertViewIs('task.show');

        $res->assertSeeText(optional($task)->name);
    }

    public function testEditPageExists()
    {
        $this->withoutExceptionHandling();

        $task = $this->tasks->first();

        $res = $this->actingAs($this->user)->get(route('tasks.edit', optional($task)->id));

        $res->assertViewIs('task.edit');
    }

    public function testUpdate()
    {
        $this->withoutExceptionHandling();

        $task = $this->tasks->first();

        $data = Task::factory()->make()->toArray();

        $res = $this->actingAs($this->user)->patch(route('tasks.update', optional($task)->id), $data);

        $res->assertRedirectToRoute('tasks.index');

        $res->assertSessionHasNoErrors();

        $updatedTask = Task::where('id', optional($task)->id)->first();

        $this->assertEquals(optional($updatedTask)->id, optional($task)->id);

        $this->assertEquals(optional($updatedTask)->name, $data['name']);
        $this->assertEquals(optional($updatedTask)->description, $data['description']);
    }

    public function testUpdateByOnlyAuthUser()
    {
        $task = $this->tasks->first();

        $data = Task::factory()->make()->toArray();

        $res = $this->patch(route('tasks.update', optional($task)->id), $data);

        $res->assertForbidden();
    }

    public function testDelete()
    {
        $this->withoutExceptionHandling();

        $data = Task::factory()->make()->toArray();
        $data['created_by_id'] = $this->user->id;

        $this->assertDatabaseCount('tasks', self::INITIAL_QUANTITY_TASKS_IN_DB);

        Task::create($data);

        $this->assertDatabaseCount('tasks', self::INITIAL_QUANTITY_TASKS_IN_DB + 1);

        $task = Task::where('created_by_id', $this->user->id)->first();

        $res = $this->actingAs($this->user)->delete(route('tasks.destroy', optional($task)->id));

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('tasks', self::INITIAL_QUANTITY_TASKS_IN_DB);

        $res->assertRedirectToRoute('tasks.index');
    }

    public function testDeleteByOnlyOwner()
    {
        $user1 = User::where('id', 1)->first();
        $user2 = User::where('id', 2)->first();

        $data1 = Task::factory()->make()->toArray();
        $data1['created_by_id'] = optional($user1)->id;

        $data2 = Task::factory()->make()->toArray();
        $data2['created_by_id'] = optional($user2)->id;

        Task::create($data1);
        Task::create($data2);

        $this->assertDatabaseCount('tasks', self::INITIAL_QUANTITY_TASKS_IN_DB + 2);

        $task1 = Task::where('created_by_id', optional($user1)->id)->first();
        $task2 = Task::where('created_by_id', optional($user2)->id)->first();

        $res = $this->actingAs($user1)->delete(route('tasks.destroy', optional($task1)->id));

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('tasks', self::INITIAL_QUANTITY_TASKS_IN_DB + 1);

        $this->actingAs($user1)->delete(route('tasks.destroy', optional($task2)->id));

        $this->assertDatabaseCount('tasks', self::INITIAL_QUANTITY_TASKS_IN_DB + 1);
    }
}

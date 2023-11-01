<?php

namespace Tests\Feature;

use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class LabelTest extends TestCase
{
    protected User $user;
    protected const INITIAL_QUANTITY_LABELS_IN_DB = 5;
    protected const INITIAL_QUANTITY_TASKSTATUSES_IN_DB = 2;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        Label::factory(self::INITIAL_QUANTITY_LABELS_IN_DB)->create();
        TaskStatus::factory(self::INITIAL_QUANTITY_TASKSTATUSES_IN_DB)->create();
    }

    public function testStore()
    {
        $this->withoutExceptionHandling();

        $data = Label::factory()->make()->toArray();

        $res = $this->actingAs($this->user)->post(route('labels.store', $data));

        $res->assertRedirectToRoute('labels.index');

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseHas('labels', [
            'name' => $data['name'],
        ]);
    }

    public function testIndexPageExists()
    {
        $this->withoutExceptionHandling();

        $res = $this->get(route('labels.index'));

        $res->assertStatus(200);

        $labels = Label::all();

        $names = $labels->pluck('name')->toArray();

        $res->assertSeeText($names);
    }

    public function testCreatePageExists()
    {
        $this->withoutExceptionHandling();

        $res = $this->actingAs($this->user)->get(route('labels.create'));

        $res->assertStatus(200);

        $res->assertSeeText('Создать метку');
    }

    public function testEditPageExists()
    {
        $this->withoutExceptionHandling();

        $label = Label::where('id', random_int(1, 5))->first();

        $res = $this->actingAs($this->user)->get(route('labels.edit', optional($label)->id));

        $res->assertStatus(200);

        $res->assertSeeText('Изменение метки');
    }

    public function testUpdate()
    {
        $this->withoutExceptionHandling();

        $label = Label::where('id', 1)->first();

        $newData = Label::factory()->make()->toArray();

        $res = $this->actingAs($this->user)->patch(route('labels.update', optional($label)->id), $newData);

        $res->assertRedirectToRoute('labels.index');

        $res->assertSessionHasNoErrors();

        $updatedLabel = Label::where('id', optional($label)->id)->first();

        $this->assertEquals(optional($updatedLabel)->name, $newData['name']);
        $this->assertEquals(optional($updatedLabel)->description, $newData['description']);
    }

    public function testDelete()
    {
        $this->withoutExceptionHandling();

        $this->assertDatabaseCount('labels', self::INITIAL_QUANTITY_LABELS_IN_DB);

        $label = Label::where('id', 1)->first();

        $res = $this->actingAs($this->user)->delete(route('labels.destroy', optional($label)->id));

        $res->assertRedirectToRoute('labels.index');

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('labels', self::INITIAL_QUANTITY_LABELS_IN_DB - 1);
    }

    public function testUpdateByOnlyAuthUser()
    {
        $label = Label::where('id', 1)->first();

        $newData = Label::factory()->make()->toArray();

        $res = $this->patch(route('labels.update', optional($label)->id), $newData);

        $res->assertForbidden();

        $labelFromDB = Label::where('id', optional($label)->id)->first();

        $this->assertNotEquals(optional($labelFromDB)->name, $newData['name']);
        $this->assertNotEquals(optional($labelFromDB)->description, $newData['description']);
    }

    public function testDeleteByOnlyAuthUser()
    {
        $this->assertDatabaseCount('labels', self::INITIAL_QUANTITY_LABELS_IN_DB);

        $label = Label::where('id', 1)->first();

        $res = $this->delete(route('labels.destroy', optional($label)->id));

        $res->assertForbidden();

        $this->assertDatabaseCount('labels', self::INITIAL_QUANTITY_LABELS_IN_DB);
    }

    public function testDeleteWhenLinkWithTaskExists()
    {
        $label = Label::where('id', 1)->first();

        $task = Task::factory()->create();

        $task->labels()->attach($label);

        $this->assertDatabaseCount('labels', self::INITIAL_QUANTITY_LABELS_IN_DB);

        $res = $this->actingAs($this->user)->delete(route('labels.destroy', optional($label)->id));

        $res->assertSessionHas('laravel_flash_message', [
            'message' => __('messages.label.deleted.error'),
            'class' => 'failure',
            'level' => null
        ]);

        $this->assertDatabaseCount('labels', self::INITIAL_QUANTITY_LABELS_IN_DB);
    }
}

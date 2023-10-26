<?php

namespace Tests\Feature;

use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Tests\TestCase;

class LabelTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        Label::factory(5)->create();
        TaskStatus::factory(1)->create();
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

    public function testUpdatePageExists()
    {
        $this->withoutExceptionHandling();

        $label = Label::where('id', random_int(1, 5))->first();

        $res = $this->actingAs($this->user)->get(route('labels.edit', $label->id));

        $res->assertStatus(200);

        $res->assertSeeText('Изменение метки');
    }

    public function testUpdate()
    {
        $this->withoutExceptionHandling();

        $label = Label::all()->first();

        $newData = Label::factory()->make()->toArray();

        $res = $this->actingAs($this->user)->patch(route('labels.update', $label->id), $newData);

        $res->assertRedirectToRoute('labels.index');

        $res->assertSessionHasNoErrors();

        $updatedLabel = Label::where('id', $label->id)->first();

        $this->assertEquals($updatedLabel->name, $newData['name']);
        $this->assertEquals($updatedLabel->description, $newData['description']);
    }

    public function testDelete()
    {
        $this->withoutExceptionHandling();

        $this->assertDatabaseCount('labels', 5);

        $label = Label::all()->first();

        $res = $this->actingAs($this->user)->delete(route('labels.destroy', $label->id));

        $res->assertRedirectToRoute('labels.index');

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('labels', 4);
    }

    public function testUpdateByOnlyAuthUser()
    {
        $label = Label::all()->first();

        $newData = Label::factory()->make()->toArray();

        $res = $this->patch(route('labels.update', $label->id), $newData);

        $res->assertRedirectToRoute('login');

        $labelFromDB = Label::where('id', $label->id)->first();

        $this->assertNotEquals($labelFromDB->name, $newData['name']);
        $this->assertNotEquals($labelFromDB->description, $newData['description']);

        $res = $this->actingAs($this->user)->patch(route('labels.update', $label->id), $newData);

        $res->assertRedirectToRoute('labels.index');

        $res->assertSessionHasNoErrors();

        $updatedLabel = Label::where('id', $label->id)->first();

        $this->assertEquals($updatedLabel->name, $newData['name']);
        $this->assertEquals($updatedLabel->description, $newData['description']);
    }

    public function testDeleteByOnlyAuthUser()
    {
        $this->assertDatabaseCount('labels', 5);

        $label = Label::all()->first();

        $this->delete(route('labels.destroy', $label->id));

        $this->assertDatabaseCount('labels', 5);

        $res = $this->actingAs($this->user)->delete(route('labels.destroy', $label->id));

        $res->assertRedirectToRoute('labels.index');

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('labels', 4);
    }

    public function testDeleteWhenLinkWithTaskExists()
    {
        $label = Label::all()->first();

        $task = Task::factory()->create();

        $task->labels()->attach($label);

        $this->assertDatabaseCount('labels', 5);

        $res = $this->actingAs($this->user)->delete(route('labels.destroy', $label->id));

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('labels', 5);

        $task->labels()->detach($label);

        $res = $this->actingAs($this->user)->delete(route('labels.destroy', $label->id));

        $res->assertSessionHasNoErrors();

        $this->assertDatabaseCount('labels', 4);
    }
}

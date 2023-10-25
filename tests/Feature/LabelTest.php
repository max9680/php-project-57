<?php

namespace Tests\Feature;

use App\Models\Label;
use App\Models\User;
use Tests\TestCase;

class LabelTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testStore()
    {
        $this->withoutExceptionHandling();

        $data = Label::factory()->make()->toArray();

        $this->actingAs($this->user)->post(route('labels.store', $data));

        $this->assertDatabaseHas('labels', [
            'name' => $data['name'],
        ]);
    }

    public function testIndexPageExists()
    {
        $this->withoutExceptionHandling();

        Label::factory(5)->create();

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

        Label::factory(5)->create();

        $label = Label::where('id', random_int(1, 5))->first();

        $res = $this->actingAs($this->user)->get(route('labels.edit', $label->id));

        $res->assertStatus(200);

        $res->assertSeeText('Изменение метки');
    }

    public function testUpdate()
    {
        $this->withoutExceptionHandling();

        $label = Label::factory()->create();

        $newData = Label::factory()->make()->toArray();

        $res = $this->actingAs($this->user)->patch(route('labels.update', $label->id), $newData);

        $res->assertRedirectToRoute('labels.index');

        $updatedLabel = Label::where('id', $label->id)->first();

        $this->assertEquals($updatedLabel->name, $newData['name']);
        $this->assertEquals($updatedLabel->description, $newData['description']);
    }

    public function testDelete()
    {
        $this->withoutExceptionHandling();

        $label = Label::factory()->create();

        $this->assertDatabaseCount('labels', 1);

        $res = $this->actingAs($this->user)->delete(route('labels.destroy', $label->id));

        $res->assertRedirectToRoute('labels.index');

        $this->assertDatabaseCount('labels', 0);
    }

    public function testUpdateByOnlyAuthUser()
    {
        $label = Label::factory()->create();

        $newData = Label::factory()->make()->toArray();

        $res = $this->patch(route('labels.update', $label->id), $newData);

        $res->assertRedirectToRoute('login');

        $labelFromDB = Label::where('id', $label->id)->first();

        $this->assertNotEquals($labelFromDB->name, $newData['name']);
        $this->assertNotEquals($labelFromDB->description, $newData['description']);

        $res = $this->actingAs($this->user)->patch(route('labels.update', $label->id), $newData);

        $res->assertRedirectToRoute('labels.index');

        $updatedLabel = Label::where('id', $label->id)->first();

        $this->assertEquals($updatedLabel->name, $newData['name']);
        $this->assertEquals($updatedLabel->description, $newData['description']);
    }

    public function testDeleteByOnlyAuthUser()
    {
        $label = Label::factory()->create();

        $this->assertDatabaseCount('labels', 1);

        $this->delete(route('labels.destroy', $label->id));

        $this->assertDatabaseCount('labels', 1);

        $res = $this->actingAs($this->user)->delete(route('labels.destroy', $label->id));

        $res->assertRedirectToRoute('labels.index');

        $this->assertDatabaseCount('labels', 0);
    }
}

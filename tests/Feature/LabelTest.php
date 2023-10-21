<?php

namespace Tests\Feature;

use App\Models\Label;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LabelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testStore()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $data = [
            'name' => 'ошибка',
            'description' => 'Какая-то ошибка в коде или проблема с функциональностью',
        ];

        $this->actingAs($user)->post(route('labels.store', $data));

        $this->assertDatabaseHas('labels', [
            'name' => $data['name'],
        ]);
    }

    /** @test */
    public function testIndex_page_exists()
    {
        $this->withoutExceptionHandling();

        Label::factory(5)->create();

        $res = $this->get(route('labels.index'));

        $res->assertStatus(200);

        $labels = Label::all();

        $names = $labels->pluck('name')->toArray();

        $res->assertSeeText($names);
    }

    /** @test */
    public function testCreate_page_exists()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $res = $this->actingAs($user)->get(route('labels.create'));

        $res->assertStatus(200);

        $res->assertSeeText('Создать метку');
    }

    /** @test */
    public function testUpdate_page_exists()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        Label::factory(5)->create();

        $label = Label::where('id', random_int(1, 5))->first();

        $res = $this->actingAs($user)->get(route('labels.edit', $label->id));

        $res->assertStatus(200);

        $res->assertSeeText('Изменение метки');
    }

    /** @test */
    public function testUpdate()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $label = Label::factory()->create();

        $newData = [
            'name' => 'edited',
            'description' => 'Edited description',
        ];

        $res = $this->actingAs($user)->patch(route('labels.update', $label->id), $newData);

        $res->assertRedirectToRoute('labels.index');

        $updatedLabel = Label::where('id', $label->id)->first();

        $this->assertEquals($updatedLabel->name, $newData['name']);
        $this->assertEquals($updatedLabel->description, $newData['description']);
    }

    /** @test */
    public function testDelete()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $label = Label::factory()->create();

        $this->assertDatabaseCount('labels', 1);

        $res = $this->actingAs($user)->delete(route('labels.destroy', $label->id));

        $res->assertRedirectToRoute('labels.index');

        $this->assertDatabaseCount('labels', 0);
    }

    /** @test */
    public function testUpdate_by_only_auth_user()
    {
        $user = User::factory()->create();

        $label = Label::factory()->create();

        $newData = [
            'name' => 'edited',
            'description' => 'Edited description',
        ];

        $res = $this->patch(route('labels.update', $label->id), $newData);

        $res->assertRedirectToRoute('login');

        $labelFromDB = Label::where('id', $label->id)->first();

        $this->assertNotEquals($labelFromDB->name, $newData['name']);
        $this->assertNotEquals($labelFromDB->description, $newData['description']);

        $res = $this->actingAs($user)->patch(route('labels.update', $label->id), $newData);

        $res->assertRedirectToRoute('labels.index');

        $updatedLabel = Label::where('id', $label->id)->first();

        $this->assertEquals($updatedLabel->name, $newData['name']);
        $this->assertEquals($updatedLabel->description, $newData['description']);
    }

    /** @test */
    public function testDelete_by_only_auth_user()
    {
        $user = User::factory()->create();

        $label = Label::factory()->create();

        $this->assertDatabaseCount('labels', 1);

        $this->delete(route('labels.destroy', $label->id));

        $this->assertDatabaseCount('labels', 1);

        $res = $this->actingAs($user)->delete(route('labels.destroy', $label->id));

        $res->assertRedirectToRoute('labels.index');

        $this->assertDatabaseCount('labels', 0);
    }
}

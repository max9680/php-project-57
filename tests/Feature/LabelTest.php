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
    public function a_label_can_be_stored()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $data = [
            'name' => 'ошибка',
            'description' => 'Какая-то ошибка в коде или проблема с функциональностью',
        ];

        $this->actingAs($user)->post('/labels', $data);

        $label = Label::first();

        $this->assertEquals($data['name'], $label->name);
    }

    /** @test */
    public function page_label_index_exists_and_display_name_and_description_for_all_labels()
    {
        $this->withoutExceptionHandling();

        Label::factory(5)->create();

        $res = $this->get('/labels');

        $res->assertStatus(200);

        $labels = Label::all();

        $names = $labels->pluck('name')->toArray();

        $res->assertSeeText($names);
    }

    /** @test */
    public function page_for_create_label_exists()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $res = $this->actingAs($user)->get('/labels/create');

        $res->assertStatus(200);

        $res->assertSeeText('Создать метку');
    }

    /** @test */
    public function page_for_update_label_exists()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        Label::factory(5)->create();

        $label = Label::where('id', random_int(1, 5))->first();

        $res = $this->actingAs($user)->get('/labels/' . $label->id . '/edit');

        $res->assertStatus(200);

        $res->assertSeeText('Изменение метки');
    }

    /** @test */
    public function a_label_can_be_update_by_auth_user()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $label = Label::factory()->create();

        $newData = [
            'name' => 'edited',
            'description' => 'Edited description',
        ];

        $res = $this->actingAs($user)->patch('/labels/' . $label->id, $newData);

        $res->assertRedirectToRoute('labels.index');

        $updatedLabel = Label::where('id', $label->id)->first();

        $this->assertEquals($updatedLabel->name, $newData['name']);
        $this->assertEquals($updatedLabel->description, $newData['description']);
    }

    /** @test */
    public function a_label_can_be_deleted_by_auth_user()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $label = Label::factory()->create();

        $this->assertDatabaseCount('labels', 1);

        $res = $this->actingAs($user)->delete('/labels/' . $label->id);

        $res->assertRedirectToRoute('labels.index');

        $this->assertDatabaseCount('labels', 0);
    }

    /** @test */
    public function a_label_can_be_update_by_only_auth_user()
    {
        $user = User::factory()->create();

        $label = Label::factory()->create();

        $newData = [
            'name' => 'edited',
            'description' => 'Edited description',
        ];

        $res = $this->patch('/labels/' . $label->id, $newData);

        $res->assertRedirectToRoute('login');

        $labelFromDB = Label::where('id', $label->id)->first();

        $this->assertNotEquals($labelFromDB->name, $newData['name']);
        $this->assertNotEquals($labelFromDB->description, $newData['description']);

        $res = $this->actingAs($user)->patch('/labels/' . $label->id, $newData);

        $res->assertRedirectToRoute('labels.index');

        $updatedLabel = Label::where('id', $label->id)->first();

        $this->assertEquals($updatedLabel->name, $newData['name']);
        $this->assertEquals($updatedLabel->description, $newData['description']);
    }

    /** @test */
    public function a_label_can_be_delete_by_only_auth_user()
    {
        $user = User::factory()->create();

        $label = Label::factory()->create();

        $this->assertDatabaseCount('labels', 1);

        $this->delete('/labels/' . $label->id);

        $this->assertDatabaseCount('labels', 1);

        $res = $this->actingAs($user)->delete('/labels/' . $label->id);

        $res->assertRedirectToRoute('labels.index');

        $this->assertDatabaseCount('labels', 0);
    }
}
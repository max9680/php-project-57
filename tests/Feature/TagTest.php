<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_tag_can_be_stored()
    {
        $this->withoutExceptionHandling();

        $data = [
            'name' => 'ошибка',
            'description' => 'Какая-то ошибка в коде или проблема с функциональностью',
        ];

        $res = $this->post('/tags', $data);

        $tag = Tag::first();

        $this->assertEquals($data['name'], $tag->name);
    }
}

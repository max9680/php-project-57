<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskStatusesTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_existing_page()
    {
        $response = $this->get('/taskStatus');

        $response->assertStatus(200);
    }

    public function test_index_page()
    {
        $response = $this->get('/taskStatus');

        $response->assertJson(fn (AssertableJson $json) =>
    $json->has('data')
        ->missing('message')
);
    }
}

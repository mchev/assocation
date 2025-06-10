<?php

use App\Models\Equipment;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guest can view home page', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Home')
        );
});

test('user can view equipment list', function () {
    $user = User::factory()->create();
    actingAsUser($user);

    Equipment::factory()->count(3)->create();

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Home')
            ->has('equipments.data', 3)
        );
});

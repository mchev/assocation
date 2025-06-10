<?php

use App\Models\Equipment;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guest can view home page', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('equipments')
        );
});

test('user can view equipment list', function () {
    $user = User::factory()->create();
    actingAsUser($user);

    Equipment::factory()->count(3)->create();

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('equipments.data', 3)
        );
});

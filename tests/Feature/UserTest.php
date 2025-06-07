<?php

use App\Models\User;
use App\Models\Organization;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user automatically gets a current organization on creation', function () {
    $user = User::factory()->create();

    expect($user->currentOrganization)->not->toBeNull()
        ->and($user->current_organization_id)->not->toBeNull();
});

test('user can set a different current organization', function () {
    $user = User::factory()->withOrganization()->create();
    $newOrg = Organization::factory()->create();
    
    // Attach user to new organization
    $user->organizations()->attach($newOrg->id, [
        'role' => 'member'
    ]);

    // Set new organization as current
    $result = $user->setPrimaryOrganization($newOrg);

    expect($result)->toBeTrue()
        ->and($user->currentOrganization->id)->toBe($newOrg->id);
});

test('user cannot set non-member organization as current', function () {
    $user = User::factory()->withOrganization()->create();
    $otherOrg = Organization::factory()->create();

    $result = $user->setPrimaryOrganization($otherOrg);

    expect($result)->toBeFalse()
        ->and($user->currentOrganization->id)->not->toBe($otherOrg->id);
});

test('user always has a current organization', function () {
    $user = User::factory()->withOrganization()->create();
    $org = $user->currentOrganization;

    // Remove current organization
    $user->update(['current_organization_id' => null]);

    // Save the user, which should trigger the saving event
    $user->save();

    // Check that a current organization was set
    expect($user->currentOrganization)->not->toBeNull();
}); 
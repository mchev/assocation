<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Tests\TestCase as BaseTestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

uses(BaseTestCase::class, RefreshDatabase::class)
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeValidationError', function () {
    return $this->toBeInstanceOf(\Illuminate\Validation\ValidationException::class);
});

expect()->extend('toHaveValidationError', function (string $field) {
    return expect($this->value->errors()->has($field))->toBeTrue();
});

expect()->extend('toBeRedirectedToLogin', function () {
    return expect($this->value->status())->toBe(302)
        ->and($this->value->headers->get('Location'))->toBe(route('login'));
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function actingAsUser(?User $user = null): TestCase
{
    return test()->actingAs($user ?? User::factory()->create());
}

function createUser(array $attributes = []): User
{
    return User::factory()->create($attributes);
}

function makeUser(array $attributes = []): User
{
    return User::factory()->make($attributes);
}

function route_without_query_params(string $route): string
{
    return explode('?', $route)[0];
}

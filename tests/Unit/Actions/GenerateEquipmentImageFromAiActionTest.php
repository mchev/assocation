<?php

use App\Actions\Equipment\GenerateEquipmentImageFromAiAction;
use App\Models\Equipment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
    config(['services.serpapi.key' => 'fake-serp-key']);
});

test('action finds image via SerpApi and downloads it when override query is used', function () {
    $equipment = Equipment::factory()->create(['name' => 'Console Soundcraft 8 pistes']);
    $imageUrl = 'https://example.com/serp-mixer.jpg';
    $fixturePath = __DIR__.'/../../fixtures/sample.jpg';
    $fakeImageBody = file_exists($fixturePath) ? file_get_contents($fixturePath) : null;
    if ($fakeImageBody === false || strlen($fakeImageBody ?? '') < 100) {
        $fakeImageBody = str_repeat('x', 2000);
    }

    Http::fake([
        'serpapi.com/search*' => Http::response([
            'images_results' => [
                ['original' => $imageUrl, 'title' => 'Soundcraft mixer'],
            ],
        ], 200),
        $imageUrl => Http::response($fakeImageBody, 200, ['Content-Type' => 'image/jpeg']),
    ]);

    $action = new GenerateEquipmentImageFromAiAction;
    $result = $action->execute($equipment, 'Soundcraft 8 track mixer product');

    expect($result)->toHaveKeys(['image', 'steps']);
    expect($result['image']->equipment_id)->toBe($equipment->id);
    expect($result['image']->original_name)->toBe('recherche-web.jpg');
    expect($equipment->images()->count())->toBe(1);
    expect($result['steps'])->toHaveCount(3);
    expect($result['steps'][0]['status'])->toBe('ok');
    expect($result['steps'][1]['status'])->toBe('ok');
    expect($result['steps'][2]['status'])->toBe('ok');
    Http::assertSent(fn ($req) => str_contains($req->url(), 'serpapi.com'));
});

test('action throws when SERPAPI_API_KEY is missing', function () {
    config(['services.serpapi.key' => null]);
    $equipment = Equipment::factory()->create(['name' => 'Console']);

    $action = new GenerateEquipmentImageFromAiAction;

    expect(fn () => $action->execute($equipment, 'mixer product'))
        ->toThrow(\RuntimeException::class, 'SERPAPI_API_KEY');
});

<?php

use App\Actions\Equipment\GenerateEquipmentImageFromAiAction;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
});

test('admin can see equipments index', function () {
    Equipment::factory()->count(2)->create();

    $response = $this->actingAs($this->admin)->get(route('admin.equipments.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Admin/Equipments/Index')
        ->has('equipments')
        ->has('filters')
    );
});

test('guest cannot see equipments index', function () {
    $response = $this->get(route('admin.equipments.index'));

    $response->assertRedirect();
});

test('non-admin user cannot see equipments index', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $response = $this->actingAs($user)->get(route('admin.equipments.index'));

    $response->assertForbidden();
});

test('admin can filter equipments without photos', function () {
    $withImage = Equipment::factory()->create();
    $withImage->images()->create([
        'path' => 'equipments/1/test.jpg',
        'original_name' => 'test.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 100,
        'order' => 0,
    ]);
    $withoutImage = Equipment::factory()->create();

    $response = $this->actingAs($this->admin)->get(route('admin.equipments.index', ['without_photos' => '1']));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Admin/Equipments/Index')
        ->has('equipments.data', 1)
        ->where('equipments.data.0.id', $withoutImage->id)
    );
});

test('admin can see equipment show', function () {
    $equipment = Equipment::factory()->create();

    $response = $this->actingAs($this->admin)->get(route('admin.equipments.show', $equipment));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Admin/Equipments/Show')
        ->has('equipment')
        ->where('equipment.id', $equipment->id)
    );
});

test('admin can upload image to equipment', function () {
    Storage::fake('s3');
    $equipment = Equipment::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg', 600, 400);

    $response = $this->actingAs($this->admin)->post(route('admin.equipments.images.store', $equipment), [
        'image' => $file,
    ], [
        'Content-Type' => 'multipart/form-data',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    expect($equipment->images()->count())->toBe(1);
});

test('admin can find and add image from web for equipment', function () {
    Storage::fake('s3');
    $equipment = Equipment::factory()->create([
        'name' => 'Console Soundcraft 8 pistes',
        'description' => 'Table de mixage 8 pistes',
    ]);
    $createdImage = $equipment->images()->create([
        'path' => 'equipments/'.$equipment->id.'/test.webp',
        'original_name' => 'recherche-web.jpg',
        'mime_type' => 'image/webp',
        'size' => 5000,
        'order' => 0,
    ]);
    $steps = [
        ['label' => 'Génération de la requête de recherche', 'status' => 'ok'],
        ['label' => "Recherche d'image sur le web", 'status' => 'ok'],
        ['label' => 'Téléchargement et enregistrement', 'status' => 'ok'],
    ];

    $this->mock(GenerateEquipmentImageFromAiAction::class, function ($mock) use ($equipment, $createdImage, $steps) {
        $mock->shouldReceive('execute')
            ->once()
            ->with(\Mockery::on(fn ($arg) => $arg->id === $equipment->id))
            ->andReturn(['image' => $createdImage, 'steps' => $steps]);
    });

    $response = $this->actingAs($this->admin)->post(route('admin.equipments.generate-image', $equipment));

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $response->assertSessionHas('generateSteps', $steps);
    expect($equipment->images()->count())->toBe(1);
    expect($equipment->images()->first()->original_name)->toBe('recherche-web.jpg');
});

test('admin can delete equipment image', function () {
    Storage::fake('s3');
    $equipment = Equipment::factory()->create();
    $image = $equipment->images()->create([
        'path' => 'equipments/'.$equipment->id.'/test.jpg',
        'original_name' => 'test.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 100,
        'order' => 0,
    ]);

    $response = $this->actingAs($this->admin)->delete(route('admin.equipments.images.destroy', [$equipment, $image]));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Photo supprimée.');
    expect($equipment->images()->count())->toBe(0);
});

<?php

use App\Actions\Equipment\DeleteEquipmentAction;
use App\Enums\ReservationStatus;
use App\Models\Category;
use App\Models\Depot;
use App\Models\Equipment;
use App\Models\EquipmentImage;
use App\Models\Organization;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->organization = Organization::factory()->create();
    $this->depot = Depot::factory()->create(['organization_id' => $this->organization->id]);
    $this->category = Category::factory()->create();

    // Add user to organization as admin
    $this->user->organizations()->attach($this->organization->id, ['role' => 'admin']);
    $this->user->update(['current_organization_id' => $this->organization->id]);

    $this->actingAs($this->user);
});

describe('Equipment Deletion', function () {
    it('can delete equipment without reservations', function () {
        $equipment = Equipment::factory()->create([
            'organization_id' => $this->organization->id,
            'depot_id' => $this->depot->id,
            'category_id' => $this->category->id,
        ]);

        $action = new DeleteEquipmentAction;
        $result = $action->execute($equipment);

        expect($result)->toBeTrue();
        expect($equipment->fresh()->trashed())->toBeTrue();
    });

    it('can delete equipment with future pending reservations', function () {
        $equipment = Equipment::factory()->create([
            'organization_id' => $this->organization->id,
            'depot_id' => $this->depot->id,
            'category_id' => $this->category->id,
        ]);

        // Create future pending reservation
        $reservation = Reservation::factory()->create([
            'from_organization_id' => $this->organization->id,
            'to_organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(15),
            'status' => ReservationStatus::PENDING,
        ]);

        $reservationItem = ReservationItem::factory()->create([
            'reservation_id' => $reservation->id,
            'equipment_id' => $equipment->id,
            'depot_id' => $this->depot->id,
        ]);

        $action = new DeleteEquipmentAction;
        $result = $action->execute($equipment);

        expect($result)->toBeTrue();
        expect($equipment->fresh()->trashed())->toBeTrue();
        expect($reservationItem->fresh()->trashed())->toBeTrue();

        // Check that reservation totals are recalculated
        $reservation->refresh();
        expect($reservation->total)->toBe(0.0);
    });

    it('cannot delete equipment with active reservations', function () {
        $equipment = Equipment::factory()->create([
            'organization_id' => $this->organization->id,
            'depot_id' => $this->depot->id,
            'category_id' => $this->category->id,
        ]);

        // Create active reservation (currently ongoing)
        $reservation = Reservation::factory()->create([
            'from_organization_id' => $this->organization->id,
            'to_organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(3),
            'status' => ReservationStatus::CONFIRMED,
        ]);

        ReservationItem::factory()->create([
            'reservation_id' => $reservation->id,
            'equipment_id' => $equipment->id,
            'depot_id' => $this->depot->id,
        ]);

        $action = new DeleteEquipmentAction;

        expect(fn () => $action->execute($equipment))
            ->toThrow(Exception::class, 'Ce matériel est actuellement en cours de location et ne peut être supprimé.');

        expect($equipment->fresh()->trashed())->toBeFalse();
    });

    it('cannot delete equipment with future confirmed reservations', function () {
        $equipment = Equipment::factory()->create([
            'organization_id' => $this->organization->id,
            'depot_id' => $this->depot->id,
            'category_id' => $this->category->id,
        ]);

        // Create future confirmed reservation
        $reservation = Reservation::factory()->create([
            'from_organization_id' => $this->organization->id,
            'to_organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(15),
            'status' => ReservationStatus::CONFIRMED,
        ]);

        ReservationItem::factory()->create([
            'reservation_id' => $reservation->id,
            'equipment_id' => $equipment->id,
            'depot_id' => $this->depot->id,
        ]);

        $action = new DeleteEquipmentAction;

        expect(fn () => $action->execute($equipment))
            ->toThrow(Exception::class, 'Ce matériel a des réservations confirmées à venir et ne peut être supprimé.');

        expect($equipment->fresh()->trashed())->toBeFalse();
    });

    it('deletes associated images when deleting equipment', function () {
        $equipment = Equipment::factory()->create([
            'organization_id' => $this->organization->id,
            'depot_id' => $this->depot->id,
            'category_id' => $this->category->id,
        ]);

        // Create equipment images
        $image1 = EquipmentImage::create([
            'equipment_id' => $equipment->id,
            'path' => 'test/path/image1.webp',
            'original_name' => 'test-image1.webp',
            'mime_type' => 'image/webp',
            'size' => 1024,
            'order' => 1,
        ]);
        $image2 = EquipmentImage::create([
            'equipment_id' => $equipment->id,
            'path' => 'test/path/image2.webp',
            'original_name' => 'test-image2.webp',
            'mime_type' => 'image/webp',
            'size' => 1024,
            'order' => 2,
        ]);

        $action = new DeleteEquipmentAction;
        $result = $action->execute($equipment);

        expect($result)->toBeTrue();
        expect($equipment->fresh()->trashed())->toBeTrue();
        expect(EquipmentImage::find($image1->id))->toBeNull();
        expect(EquipmentImage::find($image2->id))->toBeNull();
    });

    it('cancels reservation when deleting equipment removes last item', function () {
        $equipment = Equipment::factory()->create([
            'organization_id' => $this->organization->id,
            'depot_id' => $this->depot->id,
            'category_id' => $this->category->id,
        ]);

        // Create future pending reservation with only this equipment
        $reservation = Reservation::factory()->create([
            'from_organization_id' => $this->organization->id,
            'to_organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(15),
            'status' => ReservationStatus::PENDING,
        ]);

        $reservationItem = ReservationItem::factory()->create([
            'reservation_id' => $reservation->id,
            'equipment_id' => $equipment->id,
            'depot_id' => $this->depot->id,
        ]);

        $action = new DeleteEquipmentAction;
        $result = $action->execute($equipment);

        expect($result)->toBeTrue();
        expect($equipment->fresh()->trashed())->toBeTrue();
        expect($reservationItem->fresh()->trashed())->toBeTrue();

        // Check that reservation is cancelled
        $reservation->refresh();
        expect($reservation->status)->toBe(ReservationStatus::CANCELLED);
        expect($reservation->notes)->toContain('Réservation annulée automatiquement : équipement supprimé.');
    });

    it('does not cancel reservation when other items remain', function () {
        $equipment1 = Equipment::factory()->create([
            'organization_id' => $this->organization->id,
            'depot_id' => $this->depot->id,
            'category_id' => $this->category->id,
        ]);

        $equipment2 = Equipment::factory()->create([
            'organization_id' => $this->organization->id,
            'depot_id' => $this->depot->id,
            'category_id' => $this->category->id,
        ]);

        // Create future pending reservation with multiple equipment
        $reservation = Reservation::factory()->create([
            'from_organization_id' => $this->organization->id,
            'to_organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(15),
            'status' => ReservationStatus::PENDING,
        ]);

        $reservationItem1 = ReservationItem::factory()->create([
            'reservation_id' => $reservation->id,
            'equipment_id' => $equipment1->id,
            'depot_id' => $this->depot->id,
        ]);

        $reservationItem2 = ReservationItem::factory()->create([
            'reservation_id' => $reservation->id,
            'equipment_id' => $equipment2->id,
            'depot_id' => $this->depot->id,
        ]);

        $action = new DeleteEquipmentAction;
        $result = $action->execute($equipment1);

        expect($result)->toBeTrue();
        expect($equipment1->fresh()->trashed())->toBeTrue();
        expect($reservationItem1->fresh()->trashed())->toBeTrue();
        expect($equipment2->fresh()->trashed())->toBeFalse();
        expect($reservationItem2->fresh()->trashed())->toBeFalse();

        // Check that reservation is NOT cancelled
        $reservation->refresh();
        expect($reservation->status)->toBe(ReservationStatus::PENDING);
    });
});

describe('Equipment Deletion via HTTP', function () {
    it('can delete equipment via HTTP request', function () {
        $equipment = Equipment::factory()->create([
            'organization_id' => $this->organization->id,
            'depot_id' => $this->depot->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->delete(route('app.organizations.equipments.destroy', $equipment));

        $response->assertRedirect(route('app.organizations.equipments.index'));
        $response->assertSessionHas('success', 'Matériel supprimé avec succès.');
        expect($equipment->fresh()->trashed())->toBeTrue();
    });

    it('returns error when trying to delete equipment with active reservations', function () {
        $equipment = Equipment::factory()->create([
            'organization_id' => $this->organization->id,
            'depot_id' => $this->depot->id,
            'category_id' => $this->category->id,
        ]);

        // Create active reservation
        $reservation = Reservation::factory()->create([
            'from_organization_id' => $this->organization->id,
            'to_organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(3),
            'status' => ReservationStatus::CONFIRMED,
        ]);

        ReservationItem::factory()->create([
            'reservation_id' => $reservation->id,
            'equipment_id' => $equipment->id,
            'depot_id' => $this->depot->id,
        ]);

        $response = $this->delete(route('app.organizations.equipments.destroy', $equipment));

        $response->assertRedirect(route('app.organizations.equipments.edit', $equipment));
        $response->assertSessionHas('error', 'Ce matériel est actuellement en cours de location et ne peut être supprimé.');
        expect($equipment->fresh()->trashed())->toBeFalse();
    });

    it('returns error when trying to delete equipment with future confirmed reservations', function () {
        $equipment = Equipment::factory()->create([
            'organization_id' => $this->organization->id,
            'depot_id' => $this->depot->id,
            'category_id' => $this->category->id,
        ]);

        // Create future confirmed reservation
        $reservation = Reservation::factory()->create([
            'from_organization_id' => $this->organization->id,
            'to_organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(15),
            'status' => ReservationStatus::CONFIRMED,
        ]);

        ReservationItem::factory()->create([
            'reservation_id' => $reservation->id,
            'equipment_id' => $equipment->id,
            'depot_id' => $this->depot->id,
        ]);

        $response = $this->delete(route('app.organizations.equipments.destroy', $equipment));

        $response->assertRedirect(route('app.organizations.equipments.edit', $equipment));
        $response->assertSessionHas('error', 'Ce matériel a des réservations confirmées à venir et ne peut être supprimé.');
        expect($equipment->fresh()->trashed())->toBeFalse();
    });

    it('requires authorization to delete equipment', function () {
        $otherOrganization = Organization::factory()->create();
        $otherDepot = Depot::factory()->create(['organization_id' => $otherOrganization->id]);

        $equipment = Equipment::factory()->create([
            'organization_id' => $otherOrganization->id,
            'depot_id' => $otherDepot->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->delete(route('app.organizations.equipments.destroy', $equipment));

        $response->assertStatus(403);
        expect($equipment->fresh()->trashed())->toBeFalse();
    });
});

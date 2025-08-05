<?php

namespace App\Actions\Equipment;

use App\Models\Equipment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class UpdateEquipmentAction
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
    }

    public function execute(Equipment $equipment, array $data, array $images = []): Equipment
    {
        // Update the equipment
        $equipment->update([
            'name' => $data['name'],
            'brand' => $data['brand'] ?? null,
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'organization_id' => $data['organization_id'],
            'condition' => $data['condition'],
            'quantity' => $data['quantity'] ?? 1,
            'depot_id' => $data['depot_id'],
            'purchase_price' => $data['purchase_price'],
            'rental_price' => $data['rental_price'],
            'deposit_amount' => $data['deposit_amount'],
            'is_available' => $data['is_available'] ?? true,
            'requires_deposit' => $data['requires_deposit'] ?? true,
            'is_rentable' => $data['is_rentable'] ?? true,
            'last_maintenance_date' => $data['last_maintenance_date'] ?? null,
            'next_maintenance_date' => $data['next_maintenance_date'] ?? null,
        ]);

        // Handle removed images
        if (! empty($data['removed_images'])) {
            $equipment->images()
                ->whereIn('id', $data['removed_images'])
                ->get()
                ->each
                ->delete();
        }

        // Process and store new images
        if (! empty($images)) {
            $this->processImages($equipment, $images);
        }

        return $equipment->load(['category', 'depot', 'images', 'organization']);
    }

    protected function processImages(Equipment $equipment, array $images): void
    {
        foreach ($images as $index => $image) {
            if (! $image instanceof UploadedFile) {
                continue;
            }

            // Generate a unique filename
            $filename = Str::uuid().'.webp';
            $path = "equipments/{$equipment->id}/{$filename}";

            // Process image
            $processedImage = $this->manager->read($image)
                ->scaleDown(width: 1024)
                ->toWebp(80);

            // Store publicly in s3
            Storage::disk('s3')->put("{$path}", $processedImage->toFilePointer(), 'public');

            // Store image record in database
            $equipment->images()->create([
                'path' => $path,
                'original_name' => $image->getClientOriginalName(),
                'mime_type' => 'image/webp',
                'size' => $processedImage->size(),
                'order' => $index,
            ]);
        }
    }
}

<?php

namespace App\Actions\Equipment;

use App\Models\Equipment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class StoreEquipmentAction
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
    }

    public function execute(array $data, array $images = []): Equipment
    {
        // Create the equipment
        $equipment = Equipment::create([
            'organization_id' => $data['organization_id'],
            'name' => $data['name'],
            'brand' => $data['brand'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'condition' => $data['condition'],
            'quantity' => $data['quantity'],
            'depot_id' => $data['depot_id'],
            'purchase_price' => $data['purchase_price'],
            'rental_price' => $data['rental_price'],
            'deposit_amount' => $data['deposit_amount'],
            'is_available' => $data['is_available'] ?? true,
            'requires_deposit' => $data['requires_deposit'] ?? true,
            'is_rentable' => $data['is_rentable'] ?? true,
        ]);

        // Process and store images
        if (! empty($images)) {
            $this->processImages($equipment, $images);
        }

        return $equipment->load(['category', 'depot', 'images']);
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

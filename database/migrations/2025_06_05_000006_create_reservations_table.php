<?php

use App\Enums\ReservationItemStatus;
use App\Enums\ReservationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('to_organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('status')->default(ReservationStatus::PENDING->value);
            $table->text('notes')->nullable();
            $table->integer('subtotal')->default(0); // en centimes
            $table->string('discount_type')->nullable(); // percentage ou amount
            $table->integer('discount_value')->nullable();
            $table->integer('discount_amount')->default(0); // en centimes
            $table->text('discount_reason')->nullable();
            $table->integer('total')->default(0); // en centimes
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('reservation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipments')->cascadeOnDelete();
            $table->foreignId('depot_id')->constrained('depots')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->integer('price')->default(0); // en centimes
            $table->integer('total_price')->default(0); // en centimes
            $table->string('status')->default(ReservationItemStatus::PENDING->value);
            $table->dateTime('picked_up_at')->nullable();
            $table->dateTime('returned_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_items');
        Schema::dropIfExists('reservations');
    }
};

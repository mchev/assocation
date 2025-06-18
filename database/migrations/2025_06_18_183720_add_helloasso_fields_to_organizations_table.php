<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('helloasso_id')->nullable()->unique()->after('id');
            $table->string('helloasso_name')->nullable()->after('helloasso_id');
            $table->string('helloasso_slug')->nullable()->after('helloasso_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['helloasso_id', 'helloasso_name', 'helloasso_slug']);
        });
    }
};

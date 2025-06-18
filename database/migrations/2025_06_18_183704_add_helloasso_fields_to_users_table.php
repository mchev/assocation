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
        Schema::table('users', function (Blueprint $table) {
            $table->string('helloasso_id')->nullable();
            $table->string('helloasso_token')->nullable();
            $table->string('helloasso_refresh_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('helloasso_id');
            $table->dropColumn('helloasso_token');
            $table->dropColumn('helloasso_refresh_token');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('settings_shipping_costs', function (Blueprint $table) {
            $table->softDeletes(); // Isto adiciona a coluna deleted_at
        });
    }

    public function down(): void
    {
        Schema::table('settings_shipping_costs', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Remove a coluna deleted_at
        });
    }
};

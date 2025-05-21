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
        if (!Schema::hasColumn('users', 'delivery_address')) {
        $table->string('delivery_address')->nullable();
        }
        if (!Schema::hasColumn('users', 'gender')) {
            $table->enum('gender', ['F', 'M', 'O'])->nullable();
        }
        if (!Schema::hasColumn('users', 'nif')) {
            $table->string('nif')->nullable();
        }
        if (!Schema::hasColumn('users', 'payment_details')) {
            $table->string('payment_details')->nullable();
        }
        if (!Schema::hasColumn('users', 'profile_photo')) {
            $table->string('profile_photo')->nullable();
        }
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_address',
                'gender',
                'nif',
                'payment_details',
                'profile_photo',
            ]);
        });
    }
};

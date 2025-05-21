<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite does not support modifying ENUM types directly.
            // Instead, we will create a new column, copy the data, and drop the old column.
            Schema::table('users', function (Blueprint $table) {
                $table->enum('new_type', ['member', 'board', 'employee', 'pending_member'])->default('member')->after('type');
            });

            DB::table('users')->update(['new_type' => DB::raw('type')]);

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('type');
                $table->renameColumn('new_type', 'type');
            });
        } else {
            DB::statement("ALTER TABLE `users` MODIFY `type` ENUM('member', 'board', 'employee', 'pending_member') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite does not support modifying ENUM types directly.
            // Instead, we will create a new column, copy the data, and drop the old column.
            Schema::table('users', function (Blueprint $table) {
                $table->enum('new_type', ['member', 'board', 'employee'])->default('member')->after('type');
            });

            DB::table('users')->update(['new_type' => DB::raw('type')]);

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('type');
                $table->renameColumn('new_type', 'type');
            });
        } else {
            // For MySQL, we can directly modify the ENUM type.
            // Note: This will not work if there are existing values that are not in the new ENUM list.
            // You may need to handle those cases separately.
            DB::statement("ALTER TABLE `users` MODIFY `type` ENUM('member', 'board', 'employee') NOT NULL");
        }
    }
};

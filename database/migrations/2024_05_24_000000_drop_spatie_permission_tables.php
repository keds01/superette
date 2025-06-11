<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Supprimé pour compatibilité SQLite
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Supprimé pour compatibilité SQLite
    }

    public function down(): void
    {
        // Recreate tables if needed for rollback, although dropping permission tables might not require full rollback
        // This part might need adjustment based on original migration logic if a full rollback is necessary.

        throw new \RuntimeException('Dropping permission tables is irreversible.');
    }
}; 
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // PostgreSQL: se quitan las restricciones viejas y se crean índices parciales
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_email_unique');
            DB::statement('DROP INDEX IF EXISTS users_email_unique');
            DB::statement('CREATE UNIQUE INDEX users_email_activo_unique ON users (email) WHERE deleted_at IS NULL');

            DB::statement('ALTER TABLE clientes DROP CONSTRAINT IF EXISTS clientes_documento_unique');
            DB::statement('DROP INDEX IF EXISTS clientes_documento_unique');
            DB::statement('CREATE UNIQUE INDEX clientes_documento_activo_unique ON clientes (documento) WHERE deleted_at IS NULL');

            return;
        }

        // SQLite (tests): también admite índices parciales
        Schema::table('users', fn ($t) => $t->dropUnique('users_email_unique'));
        DB::statement('CREATE UNIQUE INDEX users_email_activo_unique ON users (email) WHERE deleted_at IS NULL');

        Schema::table('clientes', fn ($t) => $t->dropUnique('clientes_documento_unique'));
        DB::statement('CREATE UNIQUE INDEX clientes_documento_activo_unique ON clientes (documento) WHERE deleted_at IS NULL');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS users_email_activo_unique');
        DB::statement('DROP INDEX IF EXISTS clientes_documento_activo_unique');

        Schema::table('users', fn ($t) => $t->unique('email', 'users_email_unique'));
        Schema::table('clientes', fn ($t) => $t->unique('documento', 'clientes_documento_unique'));
    }
};

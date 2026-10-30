<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // users: el correo único solo cuenta para usuarios no borrados
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_email_unique');
        DB::statement('DROP INDEX IF EXISTS users_email_unique');
        DB::statement('CREATE UNIQUE INDEX users_email_activo_unique ON users (email) WHERE deleted_at IS NULL');

        // clientes: el documento único solo cuenta para clientes no borrados
        DB::statement('ALTER TABLE clientes DROP CONSTRAINT IF EXISTS clientes_documento_unique');
        DB::statement('DROP INDEX IF EXISTS clientes_documento_unique');
        DB::statement('CREATE UNIQUE INDEX clientes_documento_activo_unique ON clientes (documento) WHERE deleted_at IS NULL');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS users_email_activo_unique');
        DB::statement('DROP INDEX IF EXISTS clientes_documento_activo_unique');
        DB::statement('CREATE UNIQUE INDEX users_email_unique ON users (email)');
        DB::statement('CREATE UNIQUE INDEX clientes_documento_unique ON clientes (documento)');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $sqlFile = database_path('schema/database.sql');

        if (!file_exists($sqlFile)) {
            throw new \RuntimeException("Schema file not found: {$sqlFile}");
        }

        $sql = file_get_contents($sqlFile);

        DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');

        // Remove single-line comments (-- ...) but keep MySQL conditional comments (/*!...*/)
        $sql = preg_replace('/^--[^\n]*$/m', '', $sql);

        // Split on semicolons followed by newline
        $statements = preg_split('/;\s*\n/', $sql);

        foreach ($statements as $statement) {
            $statement = trim($statement);

            if (empty($statement) || $statement === 'COMMIT' || $statement === 'START TRANSACTION') {
                continue;
            }

            try {
                DB::unprepared($statement . ';');
            } catch (\Throwable $e) {
                if (str_contains($e->getMessage(), 'already exists')) {
                    continue;
                }
                // Skip charset/collation issues gracefully
                if (str_contains($e->getMessage(), 'Unknown character set')
                    || str_contains($e->getMessage(), 'Unknown collation')) {
                    continue;
                }
                throw $e;
            }
        }

        DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        // Dropping all tables is handled by migrate:fresh
    }
};

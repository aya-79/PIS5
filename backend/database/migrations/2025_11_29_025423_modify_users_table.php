<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Add missing columns safely (prevents "Duplicate column")
        if (!Schema::hasColumn('users', 'prenom')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('prenom', 100)->after('name');
            });
        }

        if (!Schema::hasColumn('users', 'telephone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('telephone', 20)->nullable()->after('prenom');
            });
        }

        if (!Schema::hasColumn('users', 'password_reset_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('password_reset_token')->nullable()->after('remember_token');
            });
        }

        if (!Schema::hasColumn('users', 'password_reset_expires_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('password_reset_expires_at')->nullable()->after('password_reset_token');
            });
        }

        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['ENSEIGNANT', 'AGENT_SCOLARITE', 'ADMINISTRATEUR'])
                      ->default('ENSEIGNANT')
                      ->after('telephone');
            });
        }
    }

    public function down(): void
    {
        // 2) Rollback safely (so rollback doesn't leave schema broken)
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'password_reset_expires_at')) {
                $table->dropColumn('password_reset_expires_at');
            }
            if (Schema::hasColumn('users', 'password_reset_token')) {
                $table->dropColumn('password_reset_token');
            }
            if (Schema::hasColumn('users', 'telephone')) {
                $table->dropColumn('telephone');
            }
            if (Schema::hasColumn('users', 'prenom')) {
                $table->dropColumn('prenom');
            }
        });
    }
};

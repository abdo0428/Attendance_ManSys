<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->dropUnique('employees_email_unique');
            $table->unique(['company_id', 'email']);
        });

        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->index('company_id');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->dropUnique('settings_key_unique');
            $table->unique(['company_id', 'key']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->index('company_id');
        });

        $userId = DB::table('users')->orderBy('id')->value('id');
        if ($userId) {
            $companyName = DB::table('settings')->where('key', 'company_name')->value('value') ?? 'Default Company';
            $now = now();
            $companyId = DB::table('companies')->insertGetId([
                'name' => $companyName,
                'owner_user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('users')->whereNull('company_id')->update(['company_id' => $companyId]);
            DB::table('employees')->whereNull('company_id')->update(['company_id' => $companyId]);
            DB::table('attendance_logs')->whereNull('company_id')->update(['company_id' => $companyId]);
            DB::table('settings')->whereNull('company_id')->update(['company_id' => $companyId]);
            DB::table('audit_logs')->whereNull('company_id')->update(['company_id' => $companyId]);
        }
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['company_id']);
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'key']);
            $table->unique('key');
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropIndex(['company_id']);
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'email']);
            $table->unique('email');
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::dropIfExists('companies');
    }
};

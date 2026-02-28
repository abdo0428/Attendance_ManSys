<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->index('work_date', 'attendance_logs_work_date_index');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->index('is_active', 'employees_is_active_index');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropIndex('attendance_logs_work_date_index');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('employees_is_active_index');
        });
    }
};


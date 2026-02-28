<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->date('work_date');                 // تاريخ الدوام
            $table->dateTime('check_in')->nullable();  // دخول
            $table->dateTime('check_out')->nullable(); // خروج

            $table->integer('worked_minutes')->default(0); // دقائق محسوبة تقريبياً
            $table->text('notes')->nullable();

            $table->unique(['employee_id','work_date']); // سجل واحد في اليوم
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
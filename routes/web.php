<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {

    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Employees
    Route::get('/employees', [EmployeeController::class,'index'])
        ->name('employees.index')->middleware('can:employees.view');

    Route::get('/employees/data', [EmployeeController::class,'data'])
        ->name('employees.data')->middleware('can:employees.view');

    Route::post('/employees', [EmployeeController::class,'store'])
        ->name('employees.store')->middleware('can:employees.create');

    Route::get('/employees/{employee}', [EmployeeController::class,'show'])
        ->name('employees.show')->middleware('can:employees.view');

    Route::put('/employees/{employee}', [EmployeeController::class,'update'])
        ->name('employees.update')->middleware('can:employees.update');

    Route::delete('/employees/{employee}', [EmployeeController::class,'destroy'])
        ->name('employees.destroy')->middleware('can:employees.delete');

    // Attendance
    Route::get('/attendance', [AttendanceController::class,'index'])
        ->name('attendance.index')->middleware('can:attendance.view');

    Route::get('/attendance/data', [AttendanceController::class,'data'])
        ->name('attendance.data')->middleware('can:attendance.view');

    Route::post('/attendance/check-in', [AttendanceController::class,'checkIn'])
        ->name('attendance.checkin')->middleware('can:attendance.checkin');

    Route::post('/attendance/check-out', [AttendanceController::class,'checkOut'])
        ->name('attendance.checkout')->middleware('can:attendance.checkout');

    Route::get('/attendance/logs/{attendanceLog}', [AttendanceController::class,'show'])
        ->name('attendance.show')->middleware('can:attendance.view');

    Route::put('/attendance/logs/{attendanceLog}', [AttendanceController::class,'update'])
        ->name('attendance.update')->middleware('can:attendance.update');

    Route::delete('/attendance/logs/{attendanceLog}', [AttendanceController::class,'destroy'])
        ->name('attendance.destroy')->middleware('can:attendance.delete');

    // Reports
    Route::get('/reports/monthly', [ReportController::class,'monthly'])
        ->name('reports.monthly')->middleware('can:reports.view');

    Route::post('/reports/monthly/data', [ReportController::class,'monthlyData'])
        ->name('reports.monthly.data')->middleware('can:reports.view');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

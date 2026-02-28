<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('landing');
})->name('landing');

Route::view('/support', 'support')->name('support');
Route::view('/privacy', 'privacy')->name('privacy');

Route::get('/locale/{locale}', function (string $locale) {
    $allowed = ['en', 'ar', 'tr'];
    if (!in_array($locale, $allowed, true)) {
        $locale = config('app.locale');
    }

    session(['locale' => $locale]);

    return redirect()->back();
})->name('locale.switch');

Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
});

Route::middleware(['auth', 'onboarded'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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

    Route::post('/attendance/absent', [AttendanceController::class,'markAbsent'])
        ->name('attendance.absent')->middleware('can:attendance.checkin');

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

    Route::get('/reports/monthly/export', [ReportController::class,'exportCsv'])
        ->name('reports.monthly.export')->middleware('can:reports.view');

    // Settings
    Route::get('/settings', [SettingsController::class, 'edit'])
        ->name('settings.edit')->middleware('can:settings.manage');
    Route::post('/settings', [SettingsController::class, 'update'])
        ->name('settings.update')->middleware('can:settings.manage');

    // Users & Roles
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index')->middleware('can:users.manage');
    Route::post('/users', [UserController::class, 'store'])
        ->name('users.store')->middleware('can:users.manage');
    Route::post('/users/{user}/role', [UserController::class, 'updateRole'])
        ->name('users.role.update')->middleware('can:users.manage');

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit.index')->middleware('can:audit.view');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// need saas and multi-tenancy support,
// make the admin can create users and edit it ,
// edit views styles 

<?php

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\LeaveRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Employee\LeaveController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Employee routes
    Route::prefix('employee')->name('employee.')->group(function () {
        // Leave Management
        Route::get('leaves', [LeaveController::class, 'index'])->name('leaves.index');
        Route::get('leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
        Route::get('leaves/data', [LeaveController::class, 'data'])->name('leaves.data');
        Route::post('leaves', [LeaveController::class, 'store'])->name('leaves.store');
        Route::delete('leaves/{id}', [LeaveController::class, 'destroy'])->name('leaves.destroy');
        Route::get('leaves/allocations', [LeaveController::class, 'allocations'])->name('leaves.allocations');
    });
    
    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware('can:admin')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Employee Management
        Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::get('employees/{user}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::get('employees/data', [EmployeeController::class, 'data'])->name('employees.data');
        Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::put('employees/{user}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('employees/{user}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
        
        // Leave Request Management
        Route::get('leave-requests', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
        Route::get('leave-requests/data', [LeaveRequestController::class, 'data'])->name('leave-requests.data');
        Route::post('leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
        Route::post('leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    });
});

require __DIR__.'/auth.php';

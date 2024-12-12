<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;

// Public Route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Auth::routes();

// Super Admin Routes
Route::middleware(['auth', 'role:super admin'])->group(function () {
    Route::get('/super-admin', [SuperAdminController::class, 'index'])->name('super-admin.index');
    Route::get('/super-admin/users', [SuperAdminController::class, 'manageUsers'])->name('super-admin.manage-users');
    Route::post('/super-admin/users/create', [SuperAdminController::class, 'createUser'])->name('super-admin.create-user');
    Route::put('/super-admin/users/{id}', [SuperAdminController::class, 'updateUser'])->name('super-admin.update-user');
    Route::delete('/super-admin/users/{id}', [SuperAdminController::class, 'deleteUser'])->name('super-admin.delete-user');
    Route::post('/super-admin/users/{id}/assign-role', [SuperAdminController::class, 'assignRole'])->name('super-admin.assign-role');
});


// Admin Routes
Route::middleware(['auth', 'role:admin|super admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::put('/admin/update-user/{id}', [AdminController::class, 'updateUser'])->name('admin.update-user');
    Route::delete('/admin/delete-user/{id}', [AdminController::class, 'deleteUser'])->name('admin.delete-user');
    Route::post('/admin/change-role/{id}', [AdminController::class, 'changeRole'])->name('admin.change-role');
});


// User Routes
Route::middleware(['auth', 'role:user|admin|super admin'])->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
});

// Protect Super Admin
Route::middleware(['auth', 'protect.superadmin'])->group(function () {
    Route::post('/super-admin/assign-role/{id}', [SuperAdminController::class, 'assignRole'])->name('super-admin.assign-role');
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

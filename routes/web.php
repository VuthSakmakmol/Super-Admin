<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UsersListController;
use App\Http\Controllers\SuperAdminController;

// Public Route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Auth::routes();

// Remove this after use
Route::get('/restore-super-admin', function () {
    $user = App\Models\User::find(1); // Replace 1 with the user's ID
    $user->assignRole('super admin');
    return 'Super Admin role restored!';
});


// Super Admin Routes
Route::middleware(['auth', 'role:super admin'])->group(function () {
    Route::get('/super-admin', [SuperAdminController::class, 'index'])->name('super-admin.index');
    Route::get('/super-admin/permissions', [SuperAdminController::class, 'managePermissions'])->name('super-admin.permissions');
    Route::get('/assign-default-roles', [SuperAdminController::class, 'assignDefaultRoles']);
    Route::put('/super-admin/update-permissions/{role}', [SuperAdminController::class, 'updatePermissions'])->name('super-admin.update-permissions'); 
    Route::get('/super-admin/activity-log', [SuperAdminController::class, 'activityLog'])->name('super-admin.activity-log');
    Route::get('/super-admin/users', [SuperAdminController::class, 'manageUsers'])->name('super-admin.manage-users');
    Route::post('/super-admin/store-user', [SuperAdminController::class, 'storeUser'])->name('super-admin.store-user');
    Route::post('/super-admin/users/create', [SuperAdminController::class, 'createUser'])->name('super-admin.create-user');
    Route::put('/super-admin/users/{id}', [SuperAdminController::class, 'updateUser'])->name('super-admin.update-user');
    Route::delete('/super-admin/users/{id}', [SuperAdminController::class, 'deleteUser'])->name('super-admin.delete-user');
    Route::post('/super-admin/users/{id}/assign-role', [SuperAdminController::class, 'assignRole'])->name('super-admin.assign-role');
});


// Admin Routes
Route::middleware(['auth', 'role:admin|super admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/users', [AdminController::class, 'manageUsers'])->name('users');
    Route::get('/reports', [AdminController::class, 'viewReports'])->name('reports');
    Route::put('/admin/update-user/{id}', [AdminController::class, 'updateUser'])->name('admin.update-user');
    Route::delete('/admin/delete-user/{id}', [AdminController::class, 'deleteUser'])->name('admin.delete-user');
    Route::post('/admin/change-role/{id}', [AdminController::class, 'changeRole'])->name('admin.change-role');
});


// User Routes
Route::middleware(['auth', 'role:user|admin|super admin'])->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/users', [AdminController::class, 'manageUsers'])->name('admin.users');
    Route::get('/user/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::get('/user/settings', [UserController::class, 'settings'])->name('user.settings');
});

// Protect Super Admin
Route::middleware(['auth', 'protect.superadmin'])->group(function () {
    Route::post('/super-admin/assign-role/{id}', [SuperAdminController::class, 'assignRole'])->name('super-admin.assign-role');
});



Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

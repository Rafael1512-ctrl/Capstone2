<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KepalaLabController;
use App\Http\Controllers\KetuaProdiController;
use App\Http\Controllers\StafAdminController;
use App\Http\Controllers\StafLabController;

// ─── AUTHENTICATION ────────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'handleLogin']);
Route::post('/logout', [AuthController::class, 'handleLogout'])->name('logout');

// ─── PROTECTED ROUTES ──────────────────────────────────────────────────────────
Route::middleware('auth.jwt')->group(function () {

    // ── DASHBOARD & GENERAL ───────────────────────────────────────────────────
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/inventory', [DashboardController::class, 'inventory'])->name('inventory');
    Route::get('/create-product', [DashboardController::class, 'showCreateProduct'])->name('create-product');
    Route::post('/create-product', [DashboardController::class, 'handleCreateProduct']);
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
    Route::get('/docs', [DashboardController::class, 'docs'])->name('docs');

    // ── ADMIN ─────────────────────────────────────────────────────────────────
    Route::middleware('role.jwt:admin')->group(function () {
        Route::get('/admin/users', [AdminController::class, 'showUsers'])->name('admin.users');
        Route::post('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
        Route::post('/admin/users/delete/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
        Route::post('/admin/users/edit/{id}', [AdminController::class, 'editUser'])->name('admin.users.edit');

        Route::get('/admin/ruangan', [AdminController::class, 'showRuangan'])->name('admin.ruangan');
        Route::post('/admin/ruangan/create', [AdminController::class, 'createRuangan'])->name('admin.ruangan.create');
        Route::post('/admin/ruangan/delete/{id}', [AdminController::class, 'deleteRuangan'])->name('admin.ruangan.delete');
        Route::post('/admin/ruangan/edit/{id}', [AdminController::class, 'editRuangan'])->name('admin.ruangan.edit');
    });

    // ── KEPALA LAB ────────────────────────────────────────────────────────────
    Route::middleware('role.jwt:kepala_lab')->group(function () {
        Route::get('/kepala-lab/pengadaan', [KepalaLabController::class, 'showPengadaan'])->name('kepala-lab.pengadaan');
        Route::post('/kepala-lab/pengadaan/create-draft', [KepalaLabController::class, 'createDraft'])->name('kepala-lab.create-draft');
        Route::post('/kepala-lab/pengadaan/update-draft/{id}', [KepalaLabController::class, 'updateDraft'])->name('kepala-lab.update-draft');
        Route::post('/kepala-lab/pengadaan/delete-draft/{id}', [KepalaLabController::class, 'deleteDraft'])->name('kepala-lab.delete-draft');
        Route::post('/kepala-lab/pengadaan/add-item', [KepalaLabController::class, 'addItem'])->name('kepala-lab.add-item');
        Route::post('/kepala-lab/pengadaan/delete-item/{id}', [KepalaLabController::class, 'deleteItem'])->name('kepala-lab.delete-item');
        Route::post('/kepala-lab/pengadaan/submit/{id}', [KepalaLabController::class, 'submitDraft'])->name('kepala-lab.submit');
        Route::get('/kepala-lab/history', [KepalaLabController::class, 'showHistory'])->name('kepala-lab.history');
    });

    // ── KETUA PRODI ───────────────────────────────────────────────────────────
    Route::middleware('role.jwt:ketua_prodi')->group(function () {
        Route::get('/ketua-prodi/review', [KetuaProdiController::class, 'showReview'])->name('ketua-prodi.review');
        Route::get('/ketua-prodi/review/{id}', [KetuaProdiController::class, 'showReviewDetail'])->name('ketua-prodi.review.detail');
        Route::post('/ketua-prodi/review/{id}/process', [KetuaProdiController::class, 'processDraft'])->name('ketua-prodi.process');
        Route::get('/ketua-prodi/history', [KetuaProdiController::class, 'showHistory'])->name('ketua-prodi.history');
    });

    // ── STAF ADMINISTRASI ─────────────────────────────────────────────────────
    Route::middleware('role.jwt:staf_admin')->group(function () {
        Route::get('/staf-admin/drafts', [StafAdminController::class, 'showDrafts'])->name('staf-admin.drafts');
        Route::get('/staf-admin/inventaris', [StafAdminController::class, 'showInventaris'])->name('staf-admin.inventaris');
        Route::post('/staf-admin/inventaris/receive/{itemId}', [StafAdminController::class, 'receiveItem'])->name('staf-admin.receive');
    });

    // ── STAF LABORATORIUM ─────────────────────────────────────────────────────
    Route::middleware('role.jwt:staf_lab')->group(function () {
        Route::get('/staf-lab/bhp', [StafLabController::class, 'showBHP'])->name('staf-lab.bhp');
        Route::post('/staf-lab/bhp/create', [StafLabController::class, 'createBHP'])->name('staf-lab.bhp.create');
        Route::post('/staf-lab/bhp/update-stock/{id}', [StafLabController::class, 'updateBHPStock'])->name('staf-lab.bhp.update');
        Route::get('/staf-lab/maintenance', [StafLabController::class, 'showMaintenance'])->name('staf-lab.maintenance');
        Route::post('/staf-lab/maintenance/create', [StafLabController::class, 'createMaintenance'])->name('staf-lab.maintenance.create');
    });
});

<?php

use App\Http\Controllers\Admin\PdfController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::livewire('/', '⚡meeting-list')->name('home');

// Auth routes
Route::livewire('/login', '⚡login')->name('login');

Route::post('/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

// Admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::livewire('/dashboard', '⚡admin.⚡dashboard')->name('dashboard');

    // Meeting routes
    Route::livewire('/meetings', '⚡admin.⚡meeting-table')->name('meetings');
    Route::livewire('/meetings/create', '⚡admin.⚡create-meeting')->name('meetings.create');
    Route::livewire('/meetings/{id}/edit', '⚡admin.⚡edit-meeting')->name('meetings.edit');
    Route::livewire('/meetings/{id}', '⚡admin.⚡meeting-detail')->name('meetings.show');

    // PDF route
    Route::get('/meetings/{meeting}/pdf', [PdfController::class, 'participantList'])
        ->name('meetings.pdf');

    // Report routes
    Route::livewire('/reports', '⚡admin.⚡report-page')->name('reports');
    Route::get('/reports/pdf', [PdfController::class, 'reportPdf'])->name('reports.pdf');

    // Notulen routes
    Route::livewire('/notulens', '⚡admin.⚡notulen-table')->name('notulens');
    Route::livewire('/notulens/create', '⚡admin.⚡create-notulen')->name('notulens.create');
    Route::livewire('/notulens/{id}/edit', '⚡admin.⚡edit-notulen')->name('notulens.edit');

    // Kepala routes
    Route::livewire('/kepalas', '⚡admin.⚡kepala-table')->name('kepalas');
    Route::livewire('/kepalas/create', '⚡admin.⚡create-kepala')->name('kepalas.create');
    Route::livewire('/kepalas/{id}/edit', '⚡admin.⚡edit-kepala')->name('kepalas.edit');

    // Notulensi routes
    Route::livewire('/notulensis', '⚡admin.⚡notulensi-table')->name('notulensis');
    Route::livewire('/notulensis/create', '⚡admin.⚡create-notulensi')->name('notulensis.create');
    Route::livewire('/notulensis/{id}/edit', '⚡admin.⚡edit-notulensi')->name('notulensis.edit');
    Route::get('/notulensis/{notulensi}/pdf', [PdfController::class, 'notulensiPdf'])->name('notulensis.pdf');

    // User routes (Super Admin only)
    Route::livewire('/users', '⚡admin.⚡user-table')
        ->name('users')
        ->middleware('role:super_admin');
    Route::livewire('/users/create', '⚡admin.⚡create-user')
        ->name('users.create')
        ->middleware('role:super_admin');
    Route::livewire('/users/{id}/edit', '⚡admin.⚡edit-user')
        ->name('users.edit')
        ->middleware('role:super_admin');
});

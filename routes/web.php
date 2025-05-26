<?php

use App\Http\Controllers\{ProfileController, AdminController, TeletravailleurController,WorkingHourController,ScreenshotController,CalendarController,ChatbotController};
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\UpdatePasswordController;


Route::redirect('/', '/login')->name('root');



require __DIR__.'/auth.php';


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/calendars', [CalendarController::class, 'index'])->name('calendars.index');
    Route::get('/calendars/{date}/tasks/create', [CalendarController::class, 'createTask'])->name('calendars.tasks.create');
    Route::post('/calendars/{date}/tasks', [CalendarController::class, 'storeTask'])->name('calendars.tasks.store');
    Route::get('/calendars/{date}/tasks/{tacheId}/edit', [CalendarController::class, 'editTask'])->name('calendars.tasks.edit');
    Route::put('/calendars/{date}/tasks/{tacheId}', [CalendarController::class, 'updateTask'])->name('calendars.tasks.update');

    Route::delete('/calendars/{date}', [CalendarController::class, 'destroy'])->name('calendars.destroy');


    Route::get('password/update', [UpdatePasswordController::class, 'show'])->name('password.update');
    Route::post('password/update', [UpdatePasswordController::class, 'update'])->name('password.update');
});


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/teletravailleurs', [AdminController::class, 'index'])->name('teletravailleurs.index');
    Route::get('/teletravailleurs/create', [AdminController::class, 'showCreateTeletravailleurForm'])->name('teletravailleur.create');
    Route::post('/teletravailleurs/store', [AdminController::class, 'storeTeletravailleur'])->name('teletravailleur.store');
    Route::delete('/teletravailleur/{id}/delete', [AdminController::class, 'destroyTeletravailleur'])->name('teletravailleur.destroy');

    Route::get('/teletravailleurs/status', [AdminController::class, 'getStatus'])->name('admin.teletravailleurs.status');

    Route::get('/teletravailleur/{id}/details', [TeletravailleurController::class, 'details'])->name('teletravailleur.details');
});


Route::middleware(['auth', 'teletravailleur'])->prefix('teletravailleur')->name('teletravailleur.')->group(function () {
    Route::get('/dashboard', [TeletravailleurController::class, 'dashboard'])->name('dashboard');


    Route::post('/working-hours/start', [WorkingHourController::class, 'start'])->name('working-hours.start');
    Route::post('/working-hours/pause', [WorkingHourController::class, 'pause'])->name('working-hours.pause');
    Route::post('/working-hours/resume', [WorkingHourController::class, 'resume'])->name('working-hours.resume');
    Route::post('/working-hours/stop', [WorkingHourController::class, 'stop'])->name('working-hours.stop');


    Route::post('/screenshots/store', [ScreenshotController::class, 'store'])->name('screenshots.store');
    Route::get('/capture', [ScreenshotController::class, 'capture']);

    Route::get('/chat', [ChatbotController::class, 'showChat'])->name('chat.index');
    Route::post('/chat/response', [ChatbotController::class, 'getResponse'])->name('chatbot.response');
    Route::post('/teletravailleur/teletravailleur/chat/clear', [ChatbotController::class, 'clearHistory'])->name('teletravailleur.chatbot.clear');
});


Route::get('/teletravailleur/complete/{token}', [TeletravailleurController::class, 'showChangePasswordForm'])->name('teletravailleur.complete');
Route::post('/teletravailleur/complete/{token}/password', [TeletravailleurController::class, 'changePassword'])->name('teletravailleur.change.password');
Route::get('/teletravailleur/complete/{token}/info', [TeletravailleurController::class, 'showCompleteProfileForm'])->name('teletravailleur.complete.info');
Route::post('/teletravailleur/complete/{token}/info', [TeletravailleurController::class, 'completeProfile'])->name('teletravailleur.complete.store');
Route::get('/teletravailleur/complete/{token}/photo', [TeletravailleurController::class, 'showUploadPhotoForm'])->name('teletravailleur.upload.photo.form');
Route::post('/teletravailleur/complete/{token}/photo', [TeletravailleurController::class, 'uploadPhoto'])->name('teletravailleur.upload.photo');

Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');







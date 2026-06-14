<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\EventRegistrationController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\StatusLookupController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\IupcRegistrationController;
use App\Http\Controllers\HackathonRegistrationController;
use App\Http\Controllers\DatathonRegistrationController;
use App\Http\Controllers\FifaRegistrationController;
use App\Http\Controllers\GamejamRegistrationController;
use App\Http\Controllers\RegistrationStatusController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ValorantRegistrationController;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/about', 'about')->name('about');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', fn () => redirect()->route('dashboard.users.index'))->name('dashboard');
    Route::get('/dashboard/status', [StatusLookupController::class, 'index'])->name('dashboard.status.index');
    Route::get('/dashboard/reports', [ReportController::class, 'index'])->name('dashboard.reports.index');
    Route::get('/dashboard/reports/download', [ReportController::class, 'download'])->name('dashboard.reports.download');
    Route::get('/dashboard/events/{event:code}', [EventRegistrationController::class, 'index'])->name('dashboard.events.registrations.index');
    Route::patch('/dashboard/events/{event:code}/registrations/{registration}/approve', [EventRegistrationController::class, 'approve'])->name('dashboard.events.registrations.approve');
    Route::patch('/dashboard/events/{event:code}/registrations/{registration}/unapprove', [EventRegistrationController::class, 'unapprove'])->name('dashboard.events.registrations.unapprove');
    Route::resource('/dashboard/users', UserController::class)
        ->except(['create', 'show'])
        ->names('dashboard.users');
});

Route::get('/status', [RegistrationStatusController::class, 'index'])->name('registration.status');

Route::get('/iupc', function () {
    return view('events.iupc');
});

Route::get('/iupc/register', [IupcRegistrationController::class, 'create'])->name('iupc.register');
Route::post('/iupc/register', [IupcRegistrationController::class, 'store'])->name('iupc.register.store');
Route::get('/iupc/register/{code}', [IupcRegistrationController::class, 'success'])->name('iupc.register.success');

Route::get('/hackathon', function () {
    return view('events.hackathon');
});

Route::get('/hackathon/register', [HackathonRegistrationController::class, 'create'])->name('hackathon.register');
Route::post('/hackathon/register', [HackathonRegistrationController::class, 'store'])->name('hackathon.register.store');
Route::get('/hackathon/register/{code}', [HackathonRegistrationController::class, 'success'])->name('hackathon.register.success');

Route::get('/datathon', function () {
    return view('events.datathon');
});

Route::get('/datathon/register', [DatathonRegistrationController::class, 'create'])->name('datathon.register');
Route::post('/datathon/register', [DatathonRegistrationController::class, 'store'])->name('datathon.register.store');
Route::get('/datathon/register/{code}', [DatathonRegistrationController::class, 'success'])->name('datathon.register.success');

Route::get('/gamejam', function () {
    return view('events.gamejam');
});

Route::get('/gamejam/register', [GamejamRegistrationController::class, 'create'])->name('gamejam.register');
Route::post('/gamejam/register', [GamejamRegistrationController::class, 'store'])->name('gamejam.register.store');
Route::get('/gamejam/register/{code}', [GamejamRegistrationController::class, 'success'])->name('gamejam.register.success');

Route::get('/fifa', function () {
    return view('events.fifa');
});

Route::get('/fifa/register', [FifaRegistrationController::class, 'create'])->name('fifa.register');
Route::post('/fifa/register', [FifaRegistrationController::class, 'store'])->name('fifa.register.store');
Route::get('/fifa/register/{code}', [FifaRegistrationController::class, 'success'])->name('fifa.register.success');

Route::get('/valorant', function () {
    return view('events.valorant');
});

Route::get('/valorant/register', [ValorantRegistrationController::class, 'create'])->name('valorant.register');
Route::post('/valorant/register', [ValorantRegistrationController::class, 'store'])->name('valorant.register.store');
Route::get('/valorant/register/{code}', [ValorantRegistrationController::class, 'success'])->name('valorant.register.success');

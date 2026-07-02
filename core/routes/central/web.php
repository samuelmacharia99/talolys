<?php

use App\Http\Controllers\Platform\Auth\LoginController as PlatformLoginController;
use App\Http\Controllers\Platform\DashboardController;
use App\Http\Controllers\Platform\DomainController;
use App\Http\Controllers\Platform\PlanController;
use App\Http\Controllers\Platform\SignupController;
use App\Http\Controllers\Platform\StripeWebhookController;
use App\Http\Controllers\Platform\TenantController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('central.home');
})->name('central.home');

Route::get('/signup', [SignupController::class, 'create'])->name('central.signup');
Route::post('/signup', [SignupController::class, 'store'])->name('central.signup.store');

Route::middleware('guest:platform')->group(function () {
    Route::get('/platform/login', [PlatformLoginController::class, 'showLoginForm'])->name('platform.login');
    Route::post('/platform/login', [PlatformLoginController::class, 'login'])->name('platform.login.submit');
});

Route::post('/platform/webhooks/stripe', [StripeWebhookController::class, 'handleWebhook'])
    ->name('platform.webhooks.stripe');

Route::middleware('auth:platform')->prefix('platform')->name('platform.')->group(function () {
    Route::post('/logout', [PlatformLoginController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('tenants', TenantController::class)->except(['destroy']);
    Route::post('tenants/{tenant}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
    Route::post('tenants/{tenant}/activate', [TenantController::class, 'activate'])->name('tenants.activate');
    Route::get('tenants/{tenant}/domains', [DomainController::class, 'index'])->name('tenants.domains');
    Route::post('tenants/{tenant}/domains', [DomainController::class, 'store'])->name('tenants.domains.store');
    Route::post('domains/{domain}/verify', [DomainController::class, 'verify'])->name('domains.verify');
    Route::resource('plans', PlanController::class)->except(['show']);
});

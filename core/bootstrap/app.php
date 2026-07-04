<?php

use App\Http\Middleware\AdminPermissionMiddleware;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\BindTenantToSession;
use App\Http\Middleware\CheckAccountOfficer;
use App\Http\Middleware\CheckModule;
use App\Http\Middleware\CheckStatus;
use App\Http\Middleware\EnsureCentralDomain;
use App\Http\Middleware\EnsureTenantSession;
use App\Http\Middleware\EnforcePlanLimits;
use App\Http\Middleware\KycMiddleware;
use App\Http\Middleware\Logout;
use App\Http\Middleware\MaintenanceMode;
use App\Http\Middleware\RedirectIfAdmin;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RedirectIfBranchStaff;
use App\Http\Middleware\RedirectIfNotAdmin;
use App\Http\Middleware\RedirectIfNotBranchStaff;
use App\Http\Middleware\RegistrationStep;
use App\Http\Middleware\ResolveTenantFromDomain;
use App\Http\Middleware\ResolveTenantFromSlug;
use App\Http\Middleware\SuperAdminPermission;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function () {
            foreach (config('tenancy.central_domains', []) as $centralDomain) {
                Route::domain($centralDomain)
                    ->middleware(['web', 'central'])
                    ->group(base_path('routes/central/web.php'));
            }

            Route::namespace('App\Http\Controllers')
                ->middleware(['tenant', 'tenant.status'])
                ->group(function () {
                    Route::prefix('api')
                        ->middleware(['api', 'maintenance', 'plan.limits'])
                        ->group(base_path('routes/api.php'));

                    Route::middleware(['web', 'tenant.session', 'bind.tenant.session'])
                        ->namespace('Admin')
                        ->prefix('admin')
                        ->name('admin.')
                        ->group(base_path('routes/admin.php'));

                    Route::middleware(['web', 'tenant.session', 'bind.tenant.session'])
                        ->namespace('BranchStaff')
                        ->prefix('staff')
                        ->name('staff.')
                        ->group(base_path('routes/branch_staff.php'));

                    Route::middleware(['web', 'maintenance', 'plan.limits'])
                        ->namespace('Gateway')
                        ->prefix('ipn')
                        ->name('ipn.')
                        ->group(base_path('routes/ipn.php'));

                    Route::middleware(['web', 'tenant.session', 'bind.tenant.session', 'maintenance', 'plan.limits'])
                        ->prefix('user')
                        ->group(base_path('routes/user.php'));

                    Route::middleware(['web', 'tenant.session', 'bind.tenant.session', 'maintenance', 'plan.limits'])
                        ->group(base_path('routes/web.php'));
                });

            Route::namespace('App\Http\Controllers')
                ->middleware(['tenant.slug'])
                ->prefix('ipn/{tenant}')
                ->where(['tenant' => '[a-z0-9\-]+'])
                ->name('ipn.slug.')
                ->group(base_path('routes/ipn.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\LanguageMiddleware::class,
            \App\Http\Middleware\ActiveTemplateMiddleware::class,
        ]);

        $middleware->group('tenant', [
            ResolveTenantFromDomain::class,
        ]);

        $middleware->group('central', [
            EnsureCentralDomain::class,
        ]);

        $middleware->alias([
            'auth.basic'            => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'cache.headers'         => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can'                   => \Illuminate\Auth\Middleware\Authorize::class,
            'auth'                  => Authenticate::class,
            'guest'                 => RedirectIfAuthenticated::class,
            'password.confirm'      => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed'                => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle'              => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified'              => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

            'adminPermission'       => AdminPermissionMiddleware::class,
            'superAdminPermission'  => SuperAdminPermission::class,
            'admin'                 => RedirectIfNotAdmin::class,
            'admin.guest'           => RedirectIfAdmin::class,

            'branch.staff'          => RedirectIfNotBranchStaff::class,
            'branch.staff.guest'    => RedirectIfBranchStaff::class,
            'checkAccountOfficer'   => CheckAccountOfficer::class,

            'check.status'          => CheckStatus::class,
            'kyc'                   => KycMiddleware::class,
            'registration.complete' => RegistrationStep::class,
            'maintenance'           => MaintenanceMode::class,

            'checkModule'           => CheckModule::class,
            'autoLogout'            => Logout::class,

            'central'               => EnsureCentralDomain::class,
            'tenant'                => ResolveTenantFromDomain::class,
            'tenant.slug'           => ResolveTenantFromSlug::class,
            'tenant.session'        => EnsureTenantSession::class,
            'bind.tenant.session'   => BindTenantToSession::class,
            'plan.limits'           => EnforcePlanLimits::class,
            'tenant.status'         => \App\Http\Middleware\CheckTenantStatus::class,
            'cron.secret'           => \App\Http\Middleware\VerifyCronSecret::class,
        ]);

        $middleware->validateCsrfTokens(
            except: ['user/deposit', 'ipn*', 'webhook*', 'platform/webhooks/*']
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function () {
            if (request()->is('api/*')) {
                return true;
            }
        });
        $exceptions->respond(function (Response $response) {
            if ($response->getStatusCode() === 401) {
                if (request()->is('api/*')) {
                    $notify[] = 'Unauthorized request';
                    return response()->json([
                        'remark' => 'unauthenticated',
                        'status' => 'error',
                        'message' => ['error' => $notify]
                    ]);
                }
            }

            return $response;
        });
    })->create();

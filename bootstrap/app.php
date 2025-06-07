<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth; // Added for auth.basic
use Illuminate\Http\Middleware\SetCacheHeaders; // Added for cache.headers
use Illuminate\Auth\Middleware\Authorize; // Added for can
use Illuminate\Routing\Middleware\ValidateSignature; // Added for signed
use Illuminate\Auth\Middleware\EnsureEmailIsVerified; // Added for verified
use Illuminate\Auth\Middleware\RequirePassword; // From L11 template
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests; // From L11 template
use Illuminate\Session\Middleware\AuthenticateSession; // From priority list

// Project specific middleware from old Kernel
use App\Http\Middleware\CheckForMaintenanceMode;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\ConvertEmptyStringsToNull; // Added from old global
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up', // Standard L11 health route
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global middleware (from old Kernel::$middleware)
        // Note: L11 typically has fewer global by default, relying on web/api groups.
        // We are mapping what was in the old $middleware property.
        // Some of these might be better suited for web/api groups specifically in pure L11.
        $middleware->use([
            CheckForMaintenanceMode::class,
            ValidatePostSize::class,
            TrimStrings::class,
            ConvertEmptyStringsToNull::class,
            TrustProxies::class,
        ]);

        // Web middleware group (from old Kernel::$middlewareGroups['web'])
        $middleware->web(append: [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ]);

        // API middleware group (from old Kernel::$middlewareGroups['api'])
        $middleware->api(append: [
            ThrottleRequests::class.':api', // Original was 'throttle:60,1', L11 default for api is 'throttle:api'
            SubstituteBindings::class, // Original was 'bindings'
        ]);

        // Route middleware aliases (from old Kernel::$routeMiddleware)
        $middleware->alias([
            'auth' => Authenticate::class,
            'auth.basic' => AuthenticateWithBasicAuth::class,
            'bindings' => SubstituteBindings::class, // Though it's also in groups by default
            'cache.headers' => SetCacheHeaders::class,
            'can' => Authorize::class,
            'guest' => RedirectIfAuthenticated::class,
            'password.confirm' => RequirePassword::class, // L11 default
            'precognitive' => HandlePrecognitiveRequests::class, // L11 default
            'signed' => ValidateSignature::class,
            'throttle' => ThrottleRequests::class, // Can be parameterized in routes
            'verified' => EnsureEmailIsVerified::class,
        ]);

        // Middleware priority (from old Kernel::$middlewarePriority)
        $middleware->priority([
            StartSession::class,
            ShareErrorsFromSession::class,
            Authenticate::class,
            AuthenticateSession::class, // This was in old priority
            SubstituteBindings::class,
            Authorize::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Based on old App\Exceptions\Handler.php:
        // $dontReport was empty.
        // No custom report() or render() logic beyond parent calls.

        $exceptions->dontFlash([
            'password',
            'password_confirmation',
        ]);
    })->create();

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'check.school.access' => \App\Http\Middleware\CheckActiveSchool::class,
            'must.change.password' => \App\Http\Middleware\MustChangePassword::class,
        ]);

        // Halaman login proyek ini bernama auth.login, sedangkan Laravel
        // mencari rute bernama 'login' persis. Tanpa arahan ini, tamu yang
        // membuka halaman aplikasi mendapat 500 "Route [login] not defined"
        // alih-alih dilempar ke halaman login.
        $middleware->redirectGuestsTo(fn () => route('auth.login'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

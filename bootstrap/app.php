<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'isAdmin' => \App\Http\Middleware\isAdmin::class,
            'isUser' => \App\Http\Middleware\isUser::class,
            'isCreator' => App\Http\Middleware\isCreator::class,
            'lang' => App\Http\Middleware\language::class

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->renderable(function (ValidationException $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                    'data' => '',
                ], 422);
            }
        });

        $exceptions->renderable(function (\Throwable $e, $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage() ?: 'Server Error',
                    'errors' => '',
                    'data' => '',
                ], 500);
            }
        });
    })->create();

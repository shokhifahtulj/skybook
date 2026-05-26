<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $e, $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'data' => null,
                'errors' => $e->errors(),
            ], 422);
        });

        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
                'data' => null,
            ], 404);
        });

        $exceptions->render(function (AuthorizationException $e, $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Akses ditolak.',
                'data' => null,
            ], 403);
        });

        $exceptions->render(function (HttpException $e, $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            if (! in_array($e->getStatusCode(), [403, 404], true)) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Permintaan ditolak.',
                'data' => null,
            ], $e->getStatusCode());
        });
    })->create();

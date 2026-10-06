<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        // Semua error di /api/* selalu JSON, walau klien lupa header Accept.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, Throwable $e) => $request->is('api/*') || $request->expectsJson()
        );

        // Kontrak Bagian 5: format 422.
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Data yang diberikan tidak valid.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // Kontrak Bagian 5: format 403, plus 404 yang tidak membocorkan nama model.
        // Satu handler untuk semua HttpException, karena Laravel mengubah
        // AuthorizationException -> AccessDeniedHttpException (403) dan
        // ModelNotFoundException -> NotFoundHttpException (404) sebelum
        // sampai ke sini, sedangkan abort(403) melempar HttpException biasa.
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return match ($e->getStatusCode()) {
                403 => response()->json([
                    'message' => 'Anda tidak memiliki akses ke sumber daya ini.',
                ], 403),
                404 => response()->json([
                    'message' => 'Sumber daya tidak ditemukan.',
                ], 404),
                default => null, // 429 dst. tetap memakai bawaan Laravel
            };
        });
    })->create();
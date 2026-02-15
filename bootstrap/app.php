<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->append(ForceJsonResponse::class);
        // Указываем, куда отправлять неавторизованных пользователей
        $middleware->authenticateSessions(); // если используете сессии

        // Глобальная настройка для API: возвращать 401 вместо редиректа
        $middleware->statefulApi();

        // Самый надежный способ для чистого API:
        $middleware->redirectTo(
            fn () => response()->json(['message' => 'Unauthenticated.'], 401)
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage() ?: 'Доступ запрещен',
                    'code' => 403
                ], 403);
            }
        });
    })->create();

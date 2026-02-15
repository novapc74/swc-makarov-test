<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(ForceJsonResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Ошибка валидации',
                'errors'  => $e->errors(),
                'code'    => 422,
            ], 422);
        });

        $exceptions->respond(function (SymfonyResponse $response, Throwable $e, Request $request) {
            if ($response->getStatusCode() === 403) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage() ?: 'У вас нет прав для этого действия.',
                    'code' => 403
                ], 403);
            }

            if ($response->getStatusCode() === 401) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Вы не авторизованы.',
                    'code' => 401
                ], 401);
            }

            return $response;
        });
    })->create();

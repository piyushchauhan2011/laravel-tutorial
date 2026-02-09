<?php

use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'validation_failed',
                    'The given data was invalid.',
                    $exception->errors(),
                    422
                );
            }
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'unauthenticated',
                    'Authentication is required.',
                    [],
                    401
                );
            }
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'forbidden',
                    'You are not allowed to perform this action.',
                    [],
                    403
                );
            }
        });

        $exceptions->render(function (ModelNotFoundException|NotFoundHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'resource_not_found',
                    'The requested resource was not found.',
                    [],
                    404
                );
            }
        });

        $exceptions->render(function (\Throwable $exception, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'server_error',
                    'An unexpected server error occurred.',
                    [],
                    500
                );
            }
        });
    })->create();

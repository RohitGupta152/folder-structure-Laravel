<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Response;
use App\Console\Commands\GetDBNameCommand;
use App\Http\Middleware\AddRequestContext;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(AddRequestContext::class);

        $middleware->alias([
            // 'auth.user' => \App\Http\Middleware\AuthenticateUser::class,
            'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'auth.admin' => \App\Http\Middleware\AdminMiddleware::class,
            'auth.sub-admin' => \App\Http\Middleware\SubAdminMiddleware::class,
            'admin_or_sub-admin' => \App\Http\Middleware\AdminOrSubAdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (AuthenticationException $exception, $request) {
            $authorizationHeader = $request->header('Authorization');

            if (!$authorizationHeader) {
                return response()->json([
                    'message' => 'User is Unauthorized.'
                ], Response::HTTP_UNAUTHORIZED);
            }

            return response()->json([
                'message' => 'Token is Invalid.'
            ], Response::HTTP_UNAUTHORIZED);
        });
    })
    ->withCommands([
        \App\Console\Commands\GetDBNameCommand::class,
        \App\Console\Commands\DatabaseConnectionTestCommand::class,
        \App\Console\Commands\MultiConnectionTestCommand::class,
        \App\Console\Commands\DatabaseQueryPerformanceCommand::class,
    ])->create();

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register your checkrole middleware
        $middleware->alias([
            'checkrole' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Add your authentication exception handling here
        $exceptions->renderable(function (AuthenticationException $exception, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                // Extract token details for debugging
                $receivedToken = $request->bearerToken();
                $tokenId = null;
                $tokenExists = false;
                $dbToken = null;

                if ($receivedToken) {
                    $parts = explode('|', $receivedToken);
                    $tokenId = $parts[0] ?? null;

                    if ($tokenId) {
                        $dbToken = PersonalAccessToken::find($tokenId);
                        $tokenExists = $dbToken !== null;
                    }
                }

                // Log detailed auth failure information
                Log::warning('Authentication Failed', [
                    'token_received' => $receivedToken ?? 'None',
                    'token_id_extracted' => $tokenId,
                    'token_exists_in_db' => $tokenExists ? 'Yes' : 'No',
                    'token_details' => $dbToken ? [
                        'id' => $dbToken->id,
                        'name' => $dbToken->name,
                        'tokenable_type' => $dbToken->tokenable_type,
                        'tokenable_id' => $dbToken->tokenable_id,
                        'created_at' => $dbToken->created_at->toDateTimeString(),
                        'last_used_at' => $dbToken->last_used_at ? $dbToken->last_used_at->toDateTimeString() : null
                    ] : 'N/A',
                    'uri' => $request->getRequestUri(),
                    'ip' => $request->ip(),
                ]);

                // Determine failure reason with more detail
                $reason = 'Unknown reason';

                if (!$receivedToken) {
                    $reason = 'No token provided';
                } elseif (strlen($receivedToken) < 20) {
                    $reason = 'Token format invalid';
                } elseif (!$tokenId) {
                    $reason = 'Token ID could not be extracted';
                } elseif (!$tokenExists) {
                    $reason = "Token ID {$tokenId} not found in database";
                } else {
                    $reason = "Token exists but authentication failed - likely user mismatch or token expired";
                }

                return response()->json([
                    'message' => 'Unauthenticated',
                    'reason' => $reason,
                    'token_debug' => [
                        'received' => $receivedToken ? substr($receivedToken, 0, 10) . '...' : 'None',
                        'id_extracted' => $tokenId,
                        'exists_in_db' => $tokenExists ? 'Yes' : 'No'
                    ],
                    'help' => 'Check token format and validity'
                ], 401);
            }
        });
    })->create();

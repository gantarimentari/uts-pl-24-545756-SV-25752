<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'database' => [
                'status' => 'healthy',
            ],
            'cache' => [
                'status' => 'healthy',
            ],
        ];

        try {
            DB::select('SELECT 1');
        } catch (Throwable $exception) {
            $checks['database'] = [
                'status' => 'unhealthy',
                'reason' => 'database_connection_or_query_failed',
            ];

            Log::error('health_database_failed', [
                'actor' => 'system_healthcheck',
                'action' => 'check_database_connection',
                'reason' => 'database_connection_or_query_failed',
                'component' => 'database',
                'exception_type' => $exception::class,
            ]);
        }

        try {
            $cacheKey = 'health_check:'.Str::uuid();
            $expectedValue = 'cache_available';

            $written = Cache::put($cacheKey, $expectedValue, 30);
            $cachedValue = Cache::get($cacheKey);
            Cache::forget($cacheKey);

            if (! $written || $cachedValue !== $expectedValue) {
                throw new RuntimeException('Cache read/write verification failed.');
            }
        } catch (Throwable $exception) {
            $checks['cache'] = [
                'status' => 'unhealthy',
                'reason' => 'cache_read_write_failed',
            ];

            Log::error('health_cache_failed', [
                'actor' => 'system_healthcheck',
                'action' => 'check_cache_read_write',
                'reason' => 'cache_read_write_failed',
                'component' => 'cache',
                'exception_type' => $exception::class,
            ]);
        }

        $healthy = $checks['database']['status'] === 'healthy'
            && $checks['cache']['status'] === 'healthy';

        return response()->json([
            'status' => $healthy ? 'healthy' : 'unhealthy',
            'checks' => $checks,
        ], $healthy ? 200 : 503);
    }
}

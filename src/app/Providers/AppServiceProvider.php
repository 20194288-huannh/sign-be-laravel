<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    const SUCCESS = 1;
    const FAIL = 1;
    public function boot(): void
    {
        Response::macro('ok', function ($code, $data = null) {
            if (!empty($data)) {
                if (is_array($data)) {
                    $data = count($data) > 0 ? $data : null;
                } elseif (is_object($data)) {
                    $data = $data->count() > 0 ? $data : null;
                } else {
                    $data = $data ? $data : null;
                }
            }

            $output = [
                'success' => self::SUCCESS,
                'data' => $data,
                'errors' => null
            ];
            return response()->json($output, $code);
        });

        Response::macro('error', function ($code, $message, $data = null) {
            $output = [
                'success' => self::FAIL,
                'data' => $data,
                'errors' => $message
            ];
            return response()->json($output, $code);
        });
    }
}

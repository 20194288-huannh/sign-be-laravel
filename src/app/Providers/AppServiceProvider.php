<?php

namespace App\Providers;

use Illuminate\Http\Response as HttpResponse;
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
    public function boot(): void
    {
        Response::macro('ok', function ($data = null, $code = 200) {
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
                'success' => 1,
                'data' => $data,
                'errors' => null
            ];
            return response()->json($output, $code);
        });

        Response::macro('error', function ($code, $message, $data = null) {
            $output = [
                'success' => 0,
                'data' => $data,
                'errors' => $message
            ];
            return response()->json($output, $code);
        });
    }
}

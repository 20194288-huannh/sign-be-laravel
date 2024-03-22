<?php

namespace App\Exceptions;

use App\Mail\SendNotificationServerError;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        Log::info("Exception server render: " . $e);
        $request->headers->set('Accept', 'application/json');
        if ($request->wantsJson()) {
            return $this->handleApiException($e);
        } else {
            return parent::render($request, $e);
        }
    }

    public function handleApiException($e)
    {
        if ($e instanceof AuthenticationException) {
            try {
                JWTAuth::parseToken()->authenticate();

                // Token is valid and not expired
                // Proceed with your logic here
            } catch (TokenExpiredException $e) {
                // Token has expired
                return response()->error(
                    Response::HTTP_UNAUTHORIZED,
                    'The authentication token has expired. Please log in again to obtain a new token.'
                );
            } catch (JWTException $e) {
                return response()->error(
                    Response::HTTP_UNAUTHORIZED,
                    'Unauthorized access. Please provide valid credentials to proceed.'
                );
            }
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->error(
                Response::HTTP_NOT_FOUND,
                'The requested resource could not be found on the server.'
            );
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->error(
                Response::HTTP_METHOD_NOT_ALLOWED,
                'The method specified in the request is not allowed for the resource.'
            );
        }

        if ($e instanceof AuthorizationException) {
            return response()->error(
                Response::HTTP_FORBIDDEN,
                'Access to the requested resource is forbidden.'
            );
        }

        if ($e instanceof ModelNotFoundException) {
            return response()->error(
                Response::HTTP_NOT_FOUND,
                '要求されたリソースはシステムに存在しません。'
            );
        }

        return response()->error(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            'An internal server error occurred. Please try again later or contact the administrator for assistance.'
        );
    }
}

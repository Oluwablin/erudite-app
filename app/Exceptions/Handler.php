<?php

namespace App\Exceptions;

use App\Traits\RespondsWithHttpStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use RespondsWithHttpStatus;

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
        $this->renderable(function (ValidationException $e) {
            return $this->error(
                message: $e->getMessage(),
                error: $e->validator->errors()->getMessages(),
                code: 422,
            );
        });

        $this->renderable(function (TokenMismatchException $e) {
            return $this->error(
                message: $e->getMessage(),
                code: $e->getCode(),
            );
        });

        $this->renderable(function (TooManyRequestsHttpException $e) {
            return $this->error(
                message: $e->getMessage(),
                code: $e->getCode(),
            );
        });

        $this->renderable(function (ModelNotFoundException $e) {
            return $this->error(
                message: $e->getMessage(),
                code: 404,
            );
        });

        $this->renderable(function (NotFoundHttpException $e) {
            return $this->error(
                message: $e->getMessage(),
                code: 404,
            );
        });

        $this->renderable(function (HttpException $e) {
            $response = new Response([
                'message' => $e->getMessage(),
            ], 419);
            $cookie = csrf_token();

            return $response->withCookie(
                cookie(
                    'XSRF-TOKEN',
                    $cookie,
                    env('SESSION_LIFETIME'),
                    "/",
                    env('SESSION_DOMAIN'),
                    null,
                    false,
                    false,
                    "lax"
                )
            );
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}

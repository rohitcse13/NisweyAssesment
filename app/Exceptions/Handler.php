<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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

        // Handle unauthenticated error
        $this->renderable(function (AuthenticationException $e) {
            return response()->json([
                'hasError' => true,
                'status' => 'error',
                'statuscode' => 401,
                'message' => 'Unauthenticated',
                'data' => null
            ], 401);
        });

        // Handle model not found error
        $this->renderable(function (ModelNotFoundException $e) {
            return response()->json([
                'hasError' => true,
                'status' => 'error',
                'statuscode' => 404,
                'message' => 'Resource not found',
                'data' => null
            ], 404);
        });

        // Handle validation error
        $this->renderable(function (ValidationException $e) {
            return response()->json([
                'hasError' => true,
                'status' => 'error',
                'statuscode' => 422,
                'message' => 'Validation failed',
                'data' => $e->errors()
            ], 422);
        });

        // Handle general HTTP errors
        $this->renderable(function (HttpException $e) {
            return response()->json([
                'hasError' => true,
                'status' => 'error',
                'statuscode' => $e->getStatusCode(),
                'message' => $e->getMessage() ?: 'HTTP error occurred',
                'data' => null
            ], $e->getStatusCode());
        });

        // Handle database query error
        // $this->renderable(function (QueryException $e) {
        //     return response()->json([
        //         'hasError' => true,
        //         'status' => 'error',
        //         'statuscode' => 500,
        //         'message' => 'An error occured.',
        //         'data' => null
        //     ], 500);
        // });

        // Handle route not found error
        $this->renderable(function (NotFoundHttpException $e) {
            return response()->json([
                'hasError' => true,
                'status' => 'error',
                'statuscode' => 404,
                'message' => 'Route not found',
                'data' => null
            ], 404);
        });

        // Catch any other unhandled exceptions
        // $this->renderable(function (Throwable $e) {
        //     return response()->json([
        //         'hasError' => true,
        //         'status' => 'error',
        //         'statuscode' => 500,
        //         'message' => 'An unexpected error occurred',
        //         'data' => null
        //     ], 500);
        // });
    }
}

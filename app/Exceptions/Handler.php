<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Psr\Log\LogLevel;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     */
    public function render($request, Throwable $e): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        // If Model Not found (e.g: not existing user error)
        if ($e instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($e->getModel()));
            return $this->errorResponse(
                "Does not exist any instance of {$model} with the given id",
                ResponseAlias::HTTP_NOT_FOUND
            );
        }

        // Handling the Unauthorized exception
        if ($e instanceof AuthorizationException) {
            return $this->errorResponse($e->getMessage(), ResponseAlias::HTTP_FORBIDDEN);
        }

        if ($e instanceof AuthenticationException) {
            return $this->errorResponse($e->getMessage(), ResponseAlias::HTTP_UNAUTHORIZED);
        }

        if ($e instanceof ValidationException) {
            return $this->errorResponse(
                $e->validator->errors()->toArray(),
                ResponseAlias::HTTP_BAD_REQUEST,
            );
        }

        if ($e instanceof FatalError) {
            return $this->errorResponse(
                __('messages.Something went wrong'),
            );
        }

        if ($e instanceof ThrottleRequestsException) {
            return $this->errorResponse(
                __('messages.Too many attempts'),
                ResponseAlias::HTTP_TOO_MANY_REQUESTS
            );
        }

        if ($e instanceof NotFoundHttpException && $request->header('Content-Type') === 'application/json') {
            return $this->errorResponse(
                __('messages.Not found'),
                ResponseAlias::HTTP_NOT_FOUND
            );
        }

        if ($e instanceof PreconditionFailedHttpException) {
            return $this->errorResponse(
                __('messages.Invalid or missing header parameter'),
                ResponseAlias::HTTP_PRECONDITION_FAILED
            );
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse(
                __('messages.This method is not supported for the route'),
                ResponseAlias::HTTP_PRECONDITION_FAILED
            );
        }

        return $this->errorResponse(
            $e
        );
    }
}

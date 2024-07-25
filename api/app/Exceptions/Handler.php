<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        // Add log levels here if needed
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        // Add exceptions here if needed
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

    // Define constants for error messages
    private const ERROR_MODEL_NOT_FOUND = 'Model not found';
    private const ERROR_QUERY_EXCEPTION = 'Query error';
    private const ERROR_METHOD_NOT_ALLOWED = 'Method not allowed';
    private const ERROR_GENERIC = 'An error occurred';
    private const ERROR_NOT_FOUND = 'Resource not found';
    private const ERROR_UNAUTHORIZED = 'Unauthorized';
    private const ERROR_FORBIDDEN = 'Forbidden';
    private const ERROR_UNAUTHENTICATED = 'Unauthenticated';
    private const ERROR_INVALID_STATUS_TRANSITION = 'Invalid status transition';
    private const ERROR_CONFLICT = 'Conflict';

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Custom reporting logic can be added here
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e): JsonResponse
    {
        if ($e instanceof ModelNotFoundException) {
            return $this->handleModelNotFound();
        }

        if ($e instanceof QueryException) {
            return $this->handleQueryException($e);
        }

        if ($e instanceof ValidationException) {
            return $this->handleValidationException($e);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return $this->handleMethodNotAllowed();
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->handleNotFound();
        }

        if ($e instanceof AuthenticationException) {
            return $this->handleUnauthenticated();
        }

        if ($e instanceof AuthorizationException) {
            return $this->handleForbidden();
        }

        if ($e instanceof HttpException) {
            return $this->handleHttpException($e);
        }
        if ($e instanceof ConflictException) {
            return $this->responseError(Response::HTTP_CONFLICT, $e->getMessage());
        }
        if ($e instanceof InvalidStatusTransitionException) {
            return $this->handleInvalidStatusTransition($e);
        }

        return $this->handleGenericException($e);
    }

    /**
     * Handle ModelNotFoundException.
     */
    protected function handleModelNotFound(): JsonResponse
    {
        return $this->responseError(Response::HTTP_NOT_FOUND, self::ERROR_MODEL_NOT_FOUND);
    }

    /**
     * Handle QueryException.
     */
    protected function handleQueryException(QueryException $e): JsonResponse
    {
        Log::error($e->getMessage());

        return $this->responseError(Response::HTTP_INTERNAL_SERVER_ERROR, self::ERROR_QUERY_EXCEPTION);
    }

    /**
     * Handle ValidationException.
     */
    protected function handleValidationException(ValidationException $e): JsonResponse
    {
        return $this->responseError(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage(), $e->errors());
    }

    /**
     * Handle MethodNotAllowedHttpException.
     */
    protected function handleMethodNotAllowed(): JsonResponse
    {
        return $this->responseError(Response::HTTP_METHOD_NOT_ALLOWED, self::ERROR_METHOD_NOT_ALLOWED);
    }

    /**
     * Handle NotFoundHttpException.
     */
    protected function handleNotFound(): JsonResponse
    {
        return $this->responseError(Response::HTTP_NOT_FOUND, self::ERROR_NOT_FOUND);
    }

    /**
     * Handle AuthenticationException.
     */
    protected function handleUnauthenticated(): JsonResponse
    {
        return $this->responseError(Response::HTTP_UNAUTHORIZED, self::ERROR_UNAUTHENTICATED);
    }

    /**
     * Handle ConflictException.
     */
    protected function handleConflict(): JsonResponse
    {
        return $this->responseError(Response::HTTP_CONFLICT, self::ERROR_CONFLICT);
    }

    /**
     * Handle AuthorizationException.
     */
    protected function handleForbidden(): JsonResponse
    {
        return $this->responseError(Response::HTTP_FORBIDDEN, self::ERROR_FORBIDDEN);
    }

    /**
     * Handle HttpException.
     */
    protected function handleHttpException(HttpException $e): JsonResponse
    {
        return $this->responseError($e->getStatusCode(), $e->getMessage());
    }

    /**
     * Handle InvalidStatusTransitionException.
     */
    protected function handleInvalidStatusTransition(InvalidStatusTransitionException $e): JsonResponse
    {
        return $this->responseError(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
    }

    /**
     * Handle generic exceptions.
     */
    protected function handleGenericException(Throwable $e): JsonResponse
    {
        Log::error($e->getMessage());

        return $this->responseError(Response::HTTP_INTERNAL_SERVER_ERROR, self::ERROR_GENERIC);
    }
}

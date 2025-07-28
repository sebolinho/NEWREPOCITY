<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class AdvancedHandler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        ValidationException::class,
        AuthenticationException::class,
        AuthorizationException::class,
        ModelNotFoundException::class,
        NotFoundHttpException::class,
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
        'new_password',
        'old_password',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Custom reporting logic with performance tracking
            $this->reportWithContext($e);
        });

        // Handle specific exception types
        $this->renderable(function (ModelNotFoundException $e, Request $request) {
            return $this->handleModelNotFound($e, $request);
        });

        $this->renderable(function (ValidationException $e, Request $request) {
            return $this->handleValidationException($e, $request);
        });

        $this->renderable(function (AuthenticationException $e, Request $request) {
            return $this->handleAuthenticationException($e, $request);
        });

        $this->renderable(function (AuthorizationException $e, Request $request) {
            return $this->handleAuthorizationException($e, $request);
        });
    }

    /**
     * Report with additional context for better debugging
     */
    private function reportWithContext(Throwable $e): void
    {
        if (!$this->shouldReport($e)) {
            return;
        }

        $context = [
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => auth()->id(),
            'route' => request()->route() ? request()->route()->getName() : null,
            'parameters' => request()->route() ? request()->route()->parameters() : [],
            'input' => $this->sanitizeInput(request()->all()),
            'memory_usage' => memory_get_peak_usage(true),
            'execution_time' => microtime(true) - LARAVEL_START,
        ];

        logger()->error('Exception occurred', [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'context' => $context
        ]);
    }

    /**
     * Sanitize input data for logging
     */
    private function sanitizeInput(array $input): array
    {
        $sanitized = [];
        $sensitiveKeys = ['password', 'password_confirmation', 'token', 'secret', 'key', 'api_key'];

        foreach ($input as $key => $value) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $sanitized[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeInput($value);
            } else {
                $sanitized[$key] = is_string($value) ? substr($value, 0, 255) : $value;
            }
        }

        return $sanitized;
    }

    /**
     * Handle Model Not Found exceptions
     */
    private function handleModelNotFound(ModelNotFoundException $e, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Resource not found',
                'message' => 'The requested resource could not be found.',
                'code' => 404
            ], 404);
        }

        // Log with context but don't report as error
        logger()->info('Model not found', [
            'model' => $e->getModel(),
            'ids' => $e->getIds(),
            'url' => $request->fullUrl(),
            'user_id' => auth()->id()
        ]);

        return response()->view('errors.404', [
            'exception' => $e,
            'title' => 'Page Not Found',
            'message' => 'The page you are looking for could not be found.'
        ], 404);
    }

    /**
     * Handle Validation exceptions
     */
    private function handleValidationException(ValidationException $e, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Validation failed',
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
                'code' => 422
            ], 422);
        }

        // Log validation failures for analysis
        logger()->info('Validation failed', [
            'errors' => $e->errors(),
            'input' => $this->sanitizeInput($request->all()),
            'url' => $request->fullUrl(),
            'user_id' => auth()->id()
        ]);

        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput($request->except($this->dontFlash));
    }

    /**
     * Handle Authentication exceptions
     */
    private function handleAuthenticationException(AuthenticationException $e, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => 'Authentication required.',
                'code' => 401
            ], 401);
        }

        logger()->info('Authentication required', [
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return redirect()->guest(route('login'))
            ->with('message', 'Please log in to access this page.');
    }

    /**
     * Handle Authorization exceptions
     */
    private function handleAuthorizationException(AuthorizationException $e, Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'You are not authorized to perform this action.',
                'code' => 403
            ], 403);
        }

        logger()->warning('Authorization denied', [
            'user_id' => auth()->id(),
            'url' => $request->fullUrl(),
            'message' => $e->getMessage()
        ]);

        return response()->view('errors.403', [
            'exception' => $e,
            'title' => 'Access Denied',
            'message' => 'You do not have permission to access this resource.'
        ], 403);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle specific HTTP exceptions
        if ($e instanceof HttpException) {
            return $this->handleHttpException($e, $request);
        }

        // Handle database connection issues
        if ($this->isDatabaseException($e)) {
            return $this->handleDatabaseException($e, $request);
        }

        // Handle timeout exceptions
        if ($this->isTimeoutException($e)) {
            return $this->handleTimeoutException($e, $request);
        }

        // Handle memory limit exceptions
        if ($this->isMemoryException($e)) {
            return $this->handleMemoryException($e, $request);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle HTTP exceptions
     */
    private function handleHttpException(HttpException $e, Request $request)
    {
        $statusCode = $e->getStatusCode();

        if ($request->expectsJson()) {
            return response()->json([
                'error' => Response::$statusTexts[$statusCode],
                'message' => $e->getMessage() ?: 'An error occurred.',
                'code' => $statusCode
            ], $statusCode);
        }

        // Try to render custom error page
        $view = "errors.{$statusCode}";
        if (view()->exists($view)) {
            return response()->view($view, [
                'exception' => $e,
                'title' => Response::$statusTexts[$statusCode],
                'message' => $e->getMessage() ?: 'An error occurred.'
            ], $statusCode);
        }

        return parent::render($request, $e);
    }

    /**
     * Check if exception is database related
     */
    private function isDatabaseException(Throwable $e): bool
    {
        return str_contains(get_class($e), 'Database') ||
               str_contains($e->getMessage(), 'database') ||
               str_contains($e->getMessage(), 'connection') ||
               str_contains($e->getMessage(), 'SQLSTATE');
    }

    /**
     * Handle database exceptions
     */
    private function handleDatabaseException(Throwable $e, Request $request)
    {
        logger()->critical('Database connection error', [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'url' => $request->fullUrl()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Service Unavailable',
                'message' => 'Database connection error. Please try again later.',
                'code' => 503
            ], 503);
        }

        return response()->view('errors.503', [
            'title' => 'Service Temporarily Unavailable',
            'message' => 'We are experiencing technical difficulties. Please try again in a few minutes.'
        ], 503);
    }

    /**
     * Check if exception is timeout related
     */
    private function isTimeoutException(Throwable $e): bool
    {
        return str_contains($e->getMessage(), 'timeout') ||
               str_contains($e->getMessage(), 'Maximum execution time');
    }

    /**
     * Handle timeout exceptions
     */
    private function handleTimeoutException(Throwable $e, Request $request)
    {
        logger()->error('Request timeout', [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'url' => $request->fullUrl(),
            'execution_time' => microtime(true) - LARAVEL_START
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Request Timeout',
                'message' => 'The request took too long to process.',
                'code' => 408
            ], 408);
        }

        return response()->view('errors.408', [
            'title' => 'Request Timeout',
            'message' => 'The request took too long to process. Please try again.'
        ], 408);
    }

    /**
     * Check if exception is memory related
     */
    private function isMemoryException(Throwable $e): bool
    {
        return str_contains($e->getMessage(), 'memory') ||
               str_contains($e->getMessage(), 'Fatal error');
    }

    /**
     * Handle memory exceptions
     */
    private function handleMemoryException(Throwable $e, Request $request)
    {
        logger()->critical('Memory limit exceeded', [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'memory_usage' => memory_get_peak_usage(true),
            'url' => $request->fullUrl()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'Server resource limit exceeded.',
                'code' => 500
            ], 500);
        }

        return response()->view('errors.500', [
            'title' => 'Server Error',
            'message' => 'The server encountered an error. Please try again later.'
        ], 500);
    }
}
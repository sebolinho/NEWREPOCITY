<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class GlobalErrorHandler
{
    /**
     * Handle an incoming request and catch any unhandled exceptions
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (ModelNotFoundException $e) {
            return $this->handleModelNotFound($e, $request);
        } catch (NotFoundHttpException $e) {
            return $this->handleNotFound($e, $request);
        } catch (Throwable $e) {
            return $this->handleGeneralException($e, $request);
        }
    }

    /**
     * Handle ModelNotFoundException
     */
    private function handleModelNotFound(ModelNotFoundException $e, Request $request)
    {
        \Log::warning('Model not found', [
            'model' => $e->getModel(),
            'ids' => $e->getIds(),
            'url' => $request->fullUrl(),
            'user_id' => auth()->id(),
            'route' => $request->route() ? $request->route()->getName() : null
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Resource not found',
                'message' => 'The requested resource could not be found.',
                'code' => 404
            ], 404);
        }

        // For admin routes, return to listing page with error
        if ($request->is('admin/*')) {
            $routeName = $request->route() ? $request->route()->getName() : '';
            $redirectRoute = $this->getAdminRedirectRoute($routeName);
            
            return redirect()
                ->route($redirectRoute)
                ->with('error', __('Resource not found. It may have been deleted or moved.'));
        }

        abort(404);
    }

    /**
     * Handle NotFoundHttpException
     */
    private function handleNotFound(NotFoundHttpException $e, Request $request)
    {
        \Log::info('Page not found', [
            'url' => $request->fullUrl(),
            'user_id' => auth()->id(),
            'referer' => $request->header('referer'),
            'user_agent' => $request->userAgent()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Page not found',
                'message' => 'The requested page could not be found.',
                'code' => 404
            ], 404);
        }

        abort(404);
    }

    /**
     * Handle general exceptions to prevent 500 errors
     */
    private function handleGeneralException(Throwable $e, Request $request)
    {
        // Log the error with full context
        \Log::error('Unhandled exception caught by GlobalErrorHandler', [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => auth()->id(),
            'route' => $request->route() ? $request->route()->getName() : null,
            'input' => $this->sanitizeInput($request->all()),
            'trace' => $e->getTraceAsString()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Internal server error',
                'message' => 'An unexpected error occurred. Please try again later.',
                'code' => 500
            ], 500);
        }

        // For admin routes, try to redirect gracefully
        if ($request->is('admin/*')) {
            $routeName = $request->route() ? $request->route()->getName() : '';
            $redirectRoute = $this->getAdminRedirectRoute($routeName);
            
            return redirect()
                ->route($redirectRoute)
                ->with('error', __('An error occurred. Please try again.'));
        }

        // For regular routes, show 500 error
        abort(500);
    }

    /**
     * Get appropriate admin redirect route based on current route
     */
    private function getAdminRedirectRoute(string $routeName): string
    {
        $routeMappings = [
            'admin.movie' => 'admin.movie.index',
            'admin.tv' => 'admin.tv.index',
            'admin.episode' => 'admin.episode.index',
            'admin.genre' => 'admin.genre.index',
            'admin.people' => 'admin.people.index',
            'admin.country' => 'admin.country.index',
            'admin.collection' => 'admin.collection.index',
            'admin.page' => 'admin.page.index',
            'admin.article' => 'admin.article.index',
            'admin.broadcast' => 'admin.broadcast.index',
            'admin.comment' => 'admin.comment.index',
            'admin.user' => 'admin.user.index',
            'admin.plan' => 'admin.plan.index',
            'admin.tax' => 'admin.tax.index',
            'admin.coupon' => 'admin.coupon.index',
            'admin.payment' => 'admin.payment.index',
            'admin.report' => 'admin.report.index',
            'admin.language' => 'admin.language.index',
            'admin.advertisement' => 'admin.advertisement.index',
            'admin.community' => 'admin.community.index',
        ];

        // Extract base route name
        foreach ($routeMappings as $baseRoute => $indexRoute) {
            if (str_starts_with($routeName, $baseRoute)) {
                return $indexRoute;
            }
        }

        // Default fallback
        return 'admin.index';
    }

    /**
     * Sanitize input for logging
     */
    private function sanitizeInput(array $input): array
    {
        $sensitiveKeys = ['password', 'password_confirmation', 'token', 'secret', 'key', 'api_key'];
        $sanitized = [];

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
}
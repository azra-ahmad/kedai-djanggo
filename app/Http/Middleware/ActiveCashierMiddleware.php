<?php

namespace App\Http\Middleware;

use App\Models\Employee;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveCashierMiddleware
{
    /**
     * Routes that are exempt from the cashier check
     */
    protected array $exemptRoutes = [
        'admin.lock-screen',
        'admin.unlock',
        'admin.lock',
        'admin.employees.index',
        'admin.employees.store',
        'admin.employees.update',
        'admin.employees.destroy',
        'admin.employees.toggle',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If no employees exist, allow access (first-time setup)
        if (Employee::count() === 0) {
            return $next($request);
        }

        // If already has active employee session, allow
        if (session()->has('active_employee')) {
            return $next($request);
        }

        // Check if current route is exempt
        $currentRoute = $request->route()?->getName();
        if ($currentRoute && in_array($currentRoute, $this->exemptRoutes)) {
            return $next($request);
        }

        // Redirect to lock screen
        return redirect()->route('admin.lock-screen');
    }
}

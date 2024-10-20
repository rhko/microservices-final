<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Rhko\UserBridge\UserService;

class ScopeCustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = (new UserService())->doRequest('get', 'scope/customer');

        if(!$response->ok()) {
            abort(401, 'unauthorized');
        }

        return $next($request);
    }
}

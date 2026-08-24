<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $moduleRoute, string $access): Response
    {
        $modules = modules();

        foreach ($modules as $module) {
            foreach ($module->details as $menu) {
                if ($menu->route === $moduleRoute) {
                    if ($menu->{$access}) {
                        return $next($request);
                    }

                    return abort(403);
                }
            }
        }

        return abort(403);
    }
}

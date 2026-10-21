<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, string $route, string $access, ?string $access2 = null): Response
    {
        $modules = modules();

        foreach ($modules as $module) {
            foreach ($module->details as $menu) {
                if ($menu->route === $route) {
                    if ($menu->{$access}) {
                        return $next($request);
                    }

                    if ($access2 !== null && $menu->{$access}) {
                        return $next($request);
                    }

                    return abort(403);
                }
            }
        }

        return abort(403);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Config;
use DB;
use Illuminate\Http\Request;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    // public function handle(Request $request, Closure $next)
    // {
    //     return $next($request);
    // }

    // public function handle($request, Closure $next)
    // {
    //     // $tenant = $request->header('X-Tenant-ID');
    //     $domainSplice = explode('.', request()->getHost());
    //     // dd(count($domainSplice));
    //     if (count($domainSplice) > 1) {
    //         $subdomain = $domainSplice[0];
    //         // $tenantDatabase = "tenant_" . $subdomain;
    //         $tenantDatabase = "multitenant_neonedu";

    //         Config::set('database.connections.tenantdb.database', $tenantDatabase);

    //         DB::reconnect('tenantdb');

    //         // Continue processing the request
    //         $response = $next($request);

    //         // Reset the database connection
    //         DB::disconnect('tenantdb');
    //     } else {
    //         $response = $next($request);
    //     }

    //     return $response;
    // }

    public function handle(Request $request, Closure $next)
    {
        $current_uri = request()->segments();
        // dd($current_uri[1]);
        if (count($current_uri) > 1 && $current_uri[0] == 'api') {
            $subdomain = $current_uri[1];
            // $tenantDatabase = "tenant_" . $subdomain;
            $tenantDatabase = "multitenant_neonedu";

            Config::set('database.connections.tenantdb.database', $tenantDatabase);

            DB::reconnect('tenantdb');

            // Continue processing the request
            $response = $next($request);

            // Reset the database connection
            DB::disconnect('tenantdb');
        } else {
            $response = $next($request);
        }

        return $response;

        // if (in_array($locale, config('app.locales'))) {
        //     \App::setLocale($locale);
        //     return $next($request);
        // }

        // if (!in_array($locale, config('app.locales'))) {

        //     $segments = $request->segments();
        //     $segments[0] = config('app.fallback_locale');

        //     return redirect(implode('/', $segments));
        // }
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Config;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SubdomainSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];

        $request->attributes->add(['subdomain' => $subdomain]);
    
        if($subdomain == 'localhost'){
            $account_data->comp_database = 'master';            
        }else{
            $subdomain = $subdomain.'.localhost:8000';
            $account_data = Company::where( 'comp_subdomain', $subdomain )->first();
            
        }
        config([
            'database.connections.mysql.host'     => '127.0.0.1',
            'database.connections.mysql.database' => $account_data->comp_database,
            'database.connections.mysql.username' => 'root',
            'database.connections.mysql.password' => '',
        ]);
        DB::reconnect();

        return $next($request);
    }
}

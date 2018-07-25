<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\DB;
use Closure;

class Customer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		if(DB::select("SELECT COUNT(*) as count FROM customers WHERE sid = '".session('customer')."' ")[0]->count > 0){
			return $next($request);
		}
        return redirect('login');
    }
}

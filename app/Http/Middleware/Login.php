<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\DB;
use Closure;

class Login
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
		if(DB::select("SELECT COUNT(*) as count FROM user WHERE secure = '".session('admin')."' ")[0]->count > 0){
			return $next($request);
		}
        return redirect('admin/login');
    }
}

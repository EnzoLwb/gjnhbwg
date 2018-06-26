<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Rent;
class GateWayRent
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
		if (request('p')=='d'){
			//查询跨库
			$res = Rent::where('RENT_DEVICENO',request('user_number'))->value('RENT_ID');
			if (!$res) {
				return response_json('0','','未被租赁状态');
			}
		}
    	return $next($request);
    }
}

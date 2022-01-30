<?php

namespace App\Http\Middleware;

use App\Helper\Reply;
use App\SmsSetting;
use Closure;

class MobileVerifyRedirect
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
        $smsSetting = SmsSetting::first();

        if ($smsSetting->nexmo_status == 'active') {
            
            if (!auth()->user()->mobile_verified) {
                if ($request->ajax()) {
                    return response(Reply::error(__('messages.front.errors.verifyMobile')));
                }

                return redirect()->back();
            }

            return $next($request);
        }

        return $next($request);
    }

}

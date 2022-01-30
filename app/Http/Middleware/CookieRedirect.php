<?php

namespace App\Http\Middleware;

use App\Helper\Reply;
use Closure;

class CookieRedirect
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

        if (!$request->hasCookie('products')) {
            if ($request->ajax()) {
                return response(Reply::error(__('messages.front.errors.atleastOneProduct')));
            }
            else {
                return redirect()->route('front.index');
            }
        }

        $products = (array)json_decode(request()->cookie('products', true));
        $keys = array_keys($products);
        $type = $products[$keys[0]]->type == 'deal' ? 'deal' : 'booking';

        if($request->route()->getName() == 'front.bookingPage' && $type == 'deal')
        {
            return redirect()->route('front.cartPage');
        }

        if ($type == 'booking' && !$request->hasCookie('bookingDetails') && $request->route()->getName() !== 'front.bookingPage') {
            return redirect()->route('front.bookingPage');
        }

        return $next($request);
    }

}

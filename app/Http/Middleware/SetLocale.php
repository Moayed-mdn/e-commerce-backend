<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {


        $supported = config('app.supported_locales',['en']);
        $locale = $request->header('locale');   

        if(!$locale || !in_array($locale,$supported)){
            $locale = $request->getPreferredLanguage($supported);
        }

        App::setLocale($locale ?: config('app.locale','en'));

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;

/**
 * Class LocaleMiddleware.
 */
class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $lang = session()->get('locale');

        if (!session()->get('locale')){
            try {
                $lang =  settings('language');
            }catch (\Exception $e){
                $lang = 'en';
            }
        }

        // Set the Laravel locale
        app()->setLocale($lang);

        // setLocale for php. Enables ->formatLocalized() with localized values for dates
        setlocale(LC_TIME, session()->get('locale'));

        // setLocale to use Carbon source locales. Enables diffForHumans() localized
        Carbon::setLocale(session()->get('locale'));

        /*
         * Set the session variable for whether or not the app is using RTL support
         * for the current language being selected
         * For use in the blade directive in BladeServiceProvider
         */
        if (session()->get('locale')) {
            session(['lang-rtl' => true]);
        } else {
            session()->forget('lang-rtl');
        }

        return $next($request);
    }
}

<?php

namespace App\Modules\Home\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * dashboard page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Exception
    */
    public function index(Request $request)
    {
        /*
        * set viewdata
        */ 
        $this->viewdata['mod_active'] = 'dashboard';
        $this->viewdata['page_title'] = 'Dashboard';

        return view('Home::content', $this->viewdata);
    }

    /**
    * set timezone
    *
    * @throws \Illuminate\Contracts\Container\BindingResolutionException
    */
    public function timezone(Request $request)
    {
        $timezone = "UTC";
        if($request->timezone && in_array($request->timezone, \DateTimeZone::listIdentifiers())) 
        {
            $timezone = $request->timezone;
            session(['timezone' => $timezone]);
        }

        session(['timezone' => $timezone]);
        return response()->json(['timezone' => session('timezone')]);
    }
}

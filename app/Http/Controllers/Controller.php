<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

use App\Models\Page;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware(function($request, $next) {
            $this->page = new Page();
            $this->viewdata = $this->page->viewdata();
            $this->viewdata['page'] = $this->page;

            $this->user = Auth::user();
            $this->viewdata['user'] = $this->user;

            $this->viewdata['mod_alias'] = null;

            return $next($request);
        });
    }
}

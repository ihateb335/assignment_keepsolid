<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{

    function cookie_connect(){
        return DB::connection(Auth::user()->role);
    }

    function get_connection_by_cookie() {
        return $_COOKIE[Auth::user()->role];
    }
}

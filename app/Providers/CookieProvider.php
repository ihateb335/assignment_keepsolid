<?php

namespace App\Providers;

class CookieProvider{
    static public function SetCookie(mixed $params = [ 'token' => null], bool $delete = false)
    {
        foreach($params as $key => $value){
            setcookie($key, $value, $delete? time() - 1000 : time() + (86400 * 1), '/',null,null,true);
        }
    }
}
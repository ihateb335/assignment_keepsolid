<?php

namespace App\Providers;

class CookieProvider{
    /**
     * 
     * @param Array $params Array with cookies
     * @param bool $delete Option to delete a cookie
     * @return void
     */
    static public function SetCookie(array $params = [ 'token' => null], bool $delete = false)
    {
        foreach($params as $key => $value){
            setcookie($key, $value, $delete? time() - 1000 : time() + (86400 * 1), '/',null,null,true);
        }
    }
}
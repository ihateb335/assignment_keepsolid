<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UserController extends Controller
{
   /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function add_fav_book(int $id, Request $request)
    {
        $book_id = $request->input('id');
        if(is_null($id)){
            return "User Id is not set";
        }
    }


}

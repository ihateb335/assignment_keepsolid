<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\Books;
use App\Models\Bookshelf;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
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

    public function registry(Request $request){
        if(is_null($request->login) ){
            return "Specify login";
        }
        if(is_null($request->password) ){
            return "Specify password";
        }

        $user = new Users;
        $user->login = $request->login;
        $user->password = Hash::make($request->password);
        $user->save();
        return "User created";

    }

    public function add_fav_book($id, Request $request)
    {
        $book_id = $request->input('id');
        if(is_null($id)){
            return "User Id is not set";
        }
        if(is_null($book_id)){
            return "Book Id is not set";
        }
        if(Users::where('id',$id)->count() == 0){
            return "There is no user with id = " . $id;
        }
        if(Books::where('id',$book_id)->count() == 0){
            return "There are no books with id = " . $book_id;
        }

        $bookshelf = new Bookshelf;
        $bookshelf->user_id = $id;
        $bookshelf->book_id = $book_id;
        $bookshelf->save();
        return "Book added to favorites";
    }


}

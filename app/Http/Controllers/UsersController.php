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
        $response = $this->validate(
            $request, [
                'login' => 'required|email|unique:users',
                'password' => 'required'
            ]
        );

        $user = new Users();
        $user->login = $request->login;
        $user->password = Hash::make($request->password);
        if($user->save()){
            $response = response()->json(
                [
                    'response' => [
                        'created' => true,
                        'userId' => $user->id
                    ]
                ], 201
            );
        }
        return $response;

    }

    public function add_fav_book(int $id, Request $request)
    {
        $response = $this->validate(
            $request, [
                'id' => 'required'
            ]
        );

        $book_id = $request->input('id');

        if(Users::where('id',$id)->count() == 0){
            $response = response()->json(
                [
                    'response' => [
                        'created' => false,
                        'error' => 'There is no user with id ' . $id
                    ]
                ], 404
            );
            return $response;
        }
        if(Books::where('id',$book_id)->count() == 0){
            $response = response()->json(
                [
                    'response' => [
                        'created' => false,
                        'error' => 'There is no book with id ' . $id
                    ]
                ], 404
            );
            return $response;
        }

        $bookshelf = new Bookshelf;
        $bookshelf->user_id = $id;
        $bookshelf->book_id = $book_id;

        if($bookshelf->save()){
            $response = response()->json(
                [
                    'response' => [
                        'created' => true,
                        'BookshelfId' => $bookshelf->id
                    ]
                ], 201
            );
        }
        return $response;
    }

    public function rem_fav_book(int $id, Request $request)
    {
        $response = $this->validate(
            $request, [
                'id' => 'required'
            ]
        );
        $book_id = $request->input('id');
       
        if(Users::where('id',$id)->count() == 0){
            $response = response()->json(
                [
                    'response' => [
                        'deleted' => false,
                        'error' => 'There is no user with id ' . $id
                    ]
                ], 404
            );
            return $response;
        }
        if(Bookshelf::where('book_id',$book_id)->where('user_id', $id)->count() == 0){
            $response = response()->json(
                [
                    'response' => [
                        'deleted' => false,
                        'error' => "There are no books in user's bookshelf with id = " . $book_id
                    ]
                ], 404
            );
            return $response;
        }

        Bookshelf::where('book_id',$book_id)->where('user_id', $id)->delete();
        $response = response()->json(
            [
                'response' => [
                    'deleted' => true,
                    'message' => 'Book on a bookshelf removed sucessfully'
                ]
            ], 204
        );
        return $response;
    }

}

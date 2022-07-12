<?php

namespace App\Http\Controllers;

use App\Providers\CookieProvider;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;

use Illuminate\Http\Request;

use Illuminate\Support\Str;
use App\Models\Users;
use App\Models\Books;
use App\Models\Bookshelf;

use Exception;

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

    public function logout(Request $request) {
        if ($request->cookie('token')) {
            CookieProvider::SetCookie(
                [
                 'token' => null
                ],
                true  
             );
            return response()->json(['quit' => 'success']);
        }
        return  response()->json(['quit' => 'no session'],404);
        
    }

    public function login(Request $request)
    {
        $this->validate($request, [

            'login' => 'required',

            'password' => 'required'

        ]);
       
        $user = Users::where('login', $request->login)->first();
        if(Hash::check($request->password, $user->password)){
            $this->logout($request);
            CookieProvider::SetCookie(
               [
                'token' => hash('sha256',$user->id . $user->password . $user->login ) 
               ]  
            );

            return response()->json(['status' => 'success']);
  
          }
          else{
              return response()->json(['status' => 'fail'],401);
          }

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
        try{ if($user->save()){
                $response = response()->json(
                    [
                        'response' => [
                            'created' => true,
                            'userId' => $user->id
                        ]
                    ], 201
                );
            }
        }
        catch(Exception $e){
            $response = response()->json(
                [
                    'response' => [
                        'created' => false,
                        'error' => $e->getMessage()
                    ]
                ], 409
            );
        }
        return $response;

    }

    /**
     * @param int $id Id of User
     */
    public function add_fav_book(int $id, Request $request)
    {
        if($id != Auth::id()){
            $response = response()->json(
                [
                    'response' => [
                        'created' => false,
                        'error' => "You cannot edit another's user bookshelf"
                    ]
                ], 403
            );
            return $response;
        }

        $book_id = $request->book_id;

        $response = $this->validate(
            $request, [
                'book_id' => ['required', RULE::unique('user.bookshelf','book_id')->where(function ($query) use($id,$book_id) {
                    return $query->where('book_id', $book_id)
                    ->where('user_id', $id);
                })]
            ]
        );

        if(Books::where('id', $book_id)->count() == 0){
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
        try{
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
        }
        catch(Exception $e){
            $response = response()->json(
                [
                    'response' => [
                        'created' => false,
                        'error' => $e->getMessage()
                    ]
                ], 409
            );
        }
        return $response;
    }
    /**
     * @param int $id Id of User
     */
    public function rem_fav_book(int $id, Request $request)
    {
        if($id != Auth::id()){
            $response = response()->json(
                [
                    'response' => [
                        'created' => false,
                        'error' => "You cannot edit another's user bookshelf"
                    ]
                ], 403
            );
            return $response;
        }

        $response = $this->validate(
            $request, [
                'book_id' => 'required'
            ]
        );
        $book_id = $request->book_id;
       
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

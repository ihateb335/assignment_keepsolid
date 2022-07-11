<?php

namespace App\Http\Controllers;

use App\Providers\CookieProvider;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
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
/*     public function test(Request $request)
    {   if($request->input == 1){
        Config::set('database.connections.guest.username','ELO_Admin');
        Config::set('database.connections.guest.password','111');
        }      

        echo $this->cookie_connect()->table('bookshelf')->get();
    } */

    public function logout() {
        CookieProvider::SetCookie(
            [
             'token' => null
            ],
            true  
         );
        return response()->json(['quit' => 'success']);
    }

    public function login(Request $request)
    {
        $this->validate($request, [

            'login' => 'required',

            'password' => 'required'

        ]);
        $credentials = $request->only(['login', 'password']);   
      /*  if(! Auth::attempt( $credentials) ){
            $user = Users::where('login', $request->login)->first();
            $this->logout();
            CookieProvider::SetCookie(
                [
                 'token' => $user->token
                ]  
             );
 
             return response()->json(['status' => 'success']);
       } */
        
        $user = Users::where('login', $request->login)->first();
        if(Hash::check($request->password, $user->password)){
            $this->logout();
            CookieProvider::SetCookie(
               [
                'token' => $user->token
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
        $user->token = base64_encode(STr::random('45'));
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
                'book_id' => ['required', RULE::unique('bookshelf','book_id')->where(function ($query) use($id,$book_id) {
                    return $query->where('book_id', $book_id)
                    ->where('user_id', $id);
                })]
            ]
        );

      
        if(Users::where('id', $id)->count() == 0){
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

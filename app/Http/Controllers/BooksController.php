<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BooksController extends Controller
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

    public function drop_book(Request $request)
    {
        return DB::table('books')->delete($request->input('id'));
    }

    public function add_book(Request $request)
    {
        $book = [
            'title' => $request->input('title'),
            'descr' => $request->input('descr')
        ];
        DB::insert('insert into books(title,descr) values (:title, :descr)', $book);
        return $book;
    }

    public function get_books(int $id = null)
    {
        if(is_null($id) || !is_int($id)){
            $users = DB::select('select * from books');
        }
        else{
            $users = DB::select("select * from books where id = $id");
        }
        return $users;
    }
}

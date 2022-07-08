<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Books;
use Illuminate\Support\Facades\Hash;

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

    public function drop_book(int $id)
    {
        return DB::table('books')->delete($id);
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

    public function CSV()
    {
        $file_name = uniqid() . '_books.csv';
        $file = fopen($file_name, 'wa');   
        foreach (Books::get() as $book) {
            fputcsv($file, [$book->id, $book->title, $book->descr ], separator: ';');
        }
        fclose($file);
        
        header("Content-Length: " . filesize($file_name));
        header("Content-Disposition: attachment; filename=".$file_name); 
        header("Content-Type: application/x-force-download; name=\"".$file_name."\"");

        readfile($file_name);
        unlink($file_name);
    }
}

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
        if(Books::where('id', $id)->count() == 0){
            $response = response()->json(
                [
                    'response' => [
                        'deleted' => false,
                        'error' => 'There is no books with id = ' . $id
                    ]
                ], 404
            );
            return $response;
        }

        DB::table('books')->delete($id);
        $response = response()->json(
            [
                'response' => [
                    'deleted' => true,
                    'message' => 'Book removed sucessfully'
                ]
            ], 204
        );
        return $response;
    }

    public function add_book(Request $request)
    {
        $response = $this->validate(
            $request, [
                'title' => 'required|unique:books'
            ]
        );

        $book = new Books();
        $book->title = $request->title;
        $book->descr= $request->descr;
        if($book->save()){
            $response = response()->json(
                [
                    'response' => [
                        'created' => true,
                        'bookId' => $book->id
                    ]
                ], 201
            );
        }

        return $response;
    }

    public function get_books(int $id = null)
    {
        if(is_null($id)){
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

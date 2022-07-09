<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Books;
use Exception;
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
        try{
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

    function CSV_Basic(){
        return Books::get()->toArray();
    }

    function CSV_Authors()
    {
        return DB::table('books as b')
        ->leftJoin('authors_books as ab','b.id','=','ab.book_id')
        ->leftJoin('authors as a','a.id','=','ab.author_id')
        ->select('b.id as book_id','b.title','b.descr', 
        'a.id as author_id', 'a.first_name', 'a.last_name', 'a.descr as author_descr')
        ->get()
        ;

    }

    function CSV_Genres()
    {
        return DB::table('books as b')
        ->leftJoin('genres_books as gb','b.id','=','gb.book_id')
        ->leftJoin('genres as g','g.id','=','gb.genre_id')
        ->select('b.id as book_id','b.title','b.descr',
         'g.id as genre_id', 'g.title as genre_title', 'g.descr as genre_descr')
        ->get()
        ;
    }

    function CSV_All(){
        return DB::table('books as b')
        ->leftJoin('genres_books as gb','b.id','=','gb.book_id')
        ->leftJoin('genres as g','g.id','=','gb.genre_id')
        ->leftJoin('authors_books as ab','b.id','=','ab.book_id')
        ->leftJoin('authors as a','a.id','=','ab.author_id')
        ->select('b.id as book_id','b.title','b.descr',
         'a.id as author_id', 'a.first_name', 'a.last_name', 'a.descr as author_descr',
          'g.id as genre_id', 'g.title as genre_title', 'g.descr as genre_descr')
        ->get()
        ;
    }

    public function CSV(Request $request, $method = '')
    {
        $sep = $request->input('sep', ';');

        $file_name = uniqid() . '_books.csv';
        $file = fopen($file_name, 'wa');

        $fields = true;
        
        $data = [];
        switch ($method) {
            case 'basic':
                $data = $this->CSV_Basic();
                break;
            case 'authors':
                $data = $this->CSV_Authors();
                break;
            case 'genres':
                $data = $this->CSV_Genres();
                break;  
            case 'all':
                $data = $this->CSV_All();
                break;              
            default:
                $data = $this->CSV_Basic();
                break;
        }

        foreach ($data as $book) {
            $arr = [];
            if($fields){
                foreach($book as $k => $v){
                    array_push($arr, $k);
                }
                $fields = false;

                fputcsv($file, $arr, separator: $sep);
                $arr = [];
            }

            foreach($book as $v){
                array_push($arr, strval($v));
            }
            
            fputcsv($file, $arr, separator: $sep);
        }
        fclose($file);
        
        header("Content-Length: " . filesize($file_name));
        header("Content-Disposition: attachment; filename=".$file_name); 
        header("Content-Type: application/x-force-download; name=\"".$file_name."\"");

        readfile($file_name);
        unlink($file_name);
    }
    
}



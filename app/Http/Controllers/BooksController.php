<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Author_Book;
use App\Models\Authors;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Books;
use App\Models\Genre_Book;
use App\Models\Genres;
use Exception;
use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rule;

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

    public function get_books(int $id = null) {
        if(is_null($id)){
            $users = Books::get();
        }
        else{
            $users = Books::where('id',$id)->get();
        }
        return $users;
    }

    public function get_genres(int $id = null) {
        if(is_null($id)){
            $users = Genres::get();
        }
        else{
            $users = Genres::where('id',$id)->get();
        }
        return $users;
    }

    public function get_authors(int $id = null) {
        if(is_null($id)){
            $users = Authors::get();
        }
        else{
            $users = Authors::where('id',$id)->get();
        }
        return $users;
    }

    public function drop_author(int $id) {
        if(Authors::where('id', $id)->count() == 0){
            $response = response()->json(
                [
                    'response' => [
                        'deleted' => false,
                        'error' => 'There is no author with id = ' . $id
                    ]
                ], 404
            );
            return $response;
        }

        DB::table('authors')->delete($id);
        $response = response()->json(
            [
                'response' => [
                    'deleted' => true,
                    'message' => 'Author removed sucessfully'
                ]
            ], 204
        );
        return $response;
    }

    public function add_author(Request $request) {
        $response = $this->validate(
            $request, [
                'first_name' => 'required',
                'last_name' => 'required'
            ]
        );

        $author = new Authors();
        $author->first_name = $request->first_name;
        $author->last_name = $request->last_name;
        $author->descr = $request->descr;

        try{
            if($author->save()){
                $response = response()->json(
                    [
                        'response' => [
                            'created' => true,
                            'authorId' => $author->id
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

    public function drop_genre(int $id) {
        if(Genres::where('id', $id)->count() == 0){
            $response = response()->json(
                [
                    'response' => [
                        'deleted' => false,
                        'error' => 'There is no genre with id = ' . $id
                    ]
                ], 404
            );
            return $response;
        }

        DB::table('genres')->delete($id);
        $response = response()->json(
            [
                'response' => [
                    'deleted' => true,
                    'message' => 'Genre removed sucessfully'
                ]
            ], 204
        );
        return $response;
    }

    public function add_genre(Request $request) {
        $response = $this->validate(
            $request, [
                'title' => 'required|unique:genres'
            ]
        );

        $genre = new Genres();
        $genre->title = $request->title;
        $genre->descr= $request->descr;
        try{
            if($genre->save()){
                $response = response()->json(
                    [
                        'response' => [
                            'created' => true,
                            'genreId' => $genre->id
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

    public function drop_book(int $id) {
        if(Books::where('id', $id)->count() == 0){
            $response = response()->json(
                [
                    'response' => [
                        'deleted' => false,
                        'error' => 'There is no book with id = ' . $id
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

    public function add_book(Request $request) {
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

    function CSV_Basic() {
        return Books::get()->toArray();
    }

    function CSV_Authors() {
        return DB::table('books as b')
        ->leftJoin('authors_books as ab','b.id','=','ab.book_id')
        ->leftJoin('authors as a','a.id','=','ab.author_id')
        ->select('b.id as book_id','b.title','b.descr', 
        'a.id as author_id', 'a.first_name', 'a.last_name', 'a.descr as author_descr')
        ->get()
        ;

    }

    function CSV_Genres() {
        return DB::table('books as b')
        ->leftJoin('genres_books as gb','b.id','=','gb.book_id')
        ->leftJoin('genres as g','g.id','=','gb.genre_id')
        ->select('b.id as book_id','b.title','b.descr',
         'g.id as genre_id', 'g.title as genre_title', 'g.descr as genre_descr')
        ->get()
        ;
    }

    function CSV_All() {
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

    public function CSV(Request $request, $method = ''){
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
    

    public function add_author_to_book(int $id, Request $request)
    {
        $book_id = $id;
        $author_id = $request->author_id;

        $response = $this->validate(
            $request, [
                'author_id' => ['required', RULE::unique('authors_books','author_id')->where(function ($query) use($author_id,$book_id) {
                    return $query->where('book_id', $book_id)
                    ->where('author_id', $author_id);
                })]
            ]
        );

      
        if(Authors::where('id', $author_id)->count() == 0){
            $response = response()->json(
                [
                    'response' => [
                        'created' => false,
                        'error' => 'There is no author with id ' . $author_id
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
                        'error' => 'There is no book with id ' . $book_id
                    ]
                ], 404
            );
            return $response;
        }

        $author_book= new Author_Book;
        $author_book->author_id = $author_id;
        $author_book->book_id = $book_id;

        try{
            if($author_book->save()){
                $response = response()->json(
                    [
                        'response' => [
                            'created' => true,
                            'Author_BookId' => $author_book->id
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
    public function add_genre_to_book(int $id, Request $request)
    {
        $book_id = $id;
        $genre_id = $request->genre_id;

        $response = $this->validate(
            $request, [
                'genre_id' => ['required', RULE::unique('genres_books','genre_id')->where(function ($query) use($genre_id,$book_id) {
                    return $query->where('book_id', $book_id)
                    ->where('genre_id', $genre_id);
                })]
            ]
        );

      
        if(Genres::where('id', $genre_id)->count() == 0){
            $response = response()->json(
                [
                    'response' => [
                        'created' => false,
                        'error' => 'There is no genre with id ' . $genre_id
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
                        'error' => 'There is no book with id ' . $book_id
                    ]
                ], 404
            );
            return $response;
        }

        $genre_book= new Genre_Book;
        $genre_book->genre_id = $genre_id;
        $genre_book->book_id = $book_id;
        
        try{
            if($genre_book->save()){
                $response = response()->json(
                    [
                        'response' => [
                            'created' => true,
                            'Genre_BookId' => $genre_book->id
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

    public function remove_author_from_book(int $id, Request $request)
    {
        $response = $this->validate(
            $request, [
                'author_id' => 'required'
            ]
        );
        $book_id = $id;
        $author_id = $request->author_id;
       
        if(Author_Book::where('author_id',$author_id)->where('book_id', $book_id)->count() == 0){
            $response = response()->json(
                [
                    'response' => [
                        'deleted' => false,
                        'error' => "There are no books with such author"
                    ]
                ], 404
            );
            return $response;
        }

        Author_Book::where('author_id',$author_id)->where('book_id',  $book_id)->delete();
        $response = response()->json(
            [
                'response' => [
                    'deleted' => true,
                    'message' => 'Book\'s author removed sucessfully'
                ]
            ], 204
        );
        return $response;
    }
    public function remove_genre_from_book(int $id, Request $request)
    {
        $response = $this->validate(
            $request, [
                'genre_id' => 'required'
            ]
        );
        $book_id = $id;
        $genre_id = $request->genre_id;
       
        if(Genre_Book::where('genre_id',$genre_id)->where('book_id', $book_id)->count() == 0){
            $response = response()->json(
                [
                    'response' => [
                        'deleted' => false,
                        'error' => "There are no books with such genre"
                    ]
                ], 404
            );
            return $response;
        }

        Genre_Book::where('genre_id',$genre_id)->where('book_id',  $book_id)->delete();
        $response = response()->json(
            [
                'response' => [
                    'deleted' => true,
                    'message' => 'Book\'s genre removed sucessfully'
                ]
            ], 204
        );
        return $response;
    }
}



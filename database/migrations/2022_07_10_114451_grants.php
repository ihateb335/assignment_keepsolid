<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
        GRANT INSERT ON users to "ELO_Guest";
        ');
        DB::statement('
        GRANT SELECT ON users, books, authors, genres, authors_books, genres_books to "ELO_Guest";
        ');
        DB::statement('
        GRANT INSERT,SELECT,UPDATE,DELETE ON users, bookshelf to "ELO_User";
        ');
        DB::statement('
        GRANT SELECT ON books, authors, genres, authors_books, genres_books to "ELO_User";
        ');
        DB::statement('
        GRANT ALL ON users, bookshelf, books, authors, genres, authors_books, genres_books to "ELO_Admin";
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('
        REVOKE INSERT ON users from "ELO_Guest"; 
        ');
        DB::statement('
        REVOKE SELECT ON users, books, authors, genres, authors_books, genres_books from "ELO_Guest";
        ');
        DB::statement('
        REVOKE INSERT,SELECT,UPDATE,DELETE ON users, bookshelf from "ELO_User";
        ');
        DB::statement('
        REVOKE SELECT ON books, authors, genres, authors_books, genres_books from "ELO_User";
        ');
        DB::statement('
        REVOKE ALL ON users, bookshelf, books, authors, genres, authors_books, genres_books from "ELO_Admin";
        ');
    }
};

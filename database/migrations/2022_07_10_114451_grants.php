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
        DB::statement("
        DO
        $$
        BEGIN
        IF EXISTS (
            SELECT FROM pg_catalog.pg_roles
            WHERE  rolname in ('ELO_User','ELO_Guest','ELO_Admin')) THEN

            RAISE NOTICE 'Roles already exists. Skipping.';
        ELSE
            CREATE ROLE \"ELO_User\" LOGIN PASSWORD '84enwFxn';
            CREATE ROLE \"ELO_Admin\" LOGIN PASSWORD 'R3XdQKmW';
            CREATE ROLE \"ELO_Guest\" LOGIN PASSWORD 'pp6MwkG9';
        END IF;
        END
        $$;
        ");

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

        DB::statement('
        GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO "ELO_Guest";
        ');
        DB::statement('
        GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO "ELO_User";
        ');
        DB::statement('
        GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO "ELO_Admin";
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

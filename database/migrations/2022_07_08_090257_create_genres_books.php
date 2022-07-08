<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genres_books', function (Blueprint $table) {
            $table->id();
            
            $table
            ->foreignId('genre_id')
            ->constrained('genres')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table 
            ->foreignId('book_id')
            ->constrained('books')
            ->onUpdate('cascade')
            ->onDelete('cascade');

            $table->unique(['genre_id', 'book_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('genres_books');
    }
};

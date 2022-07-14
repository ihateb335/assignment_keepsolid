<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


$router->group(['prefix' => 'users/'], function () use ($router) {
    $router->post('registry', 'UsersController@registry');

    $router->post('login', 'UsersController@login');
    $router->post('logout', 'UsersController@logout');
    
    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->post('add_to_favorite/{id:[0-9]+}', 'UsersController@add_fav_book');
        $router->delete('remove_from_favorite/{id:[0-9]+}', 'UsersController@rem_fav_book');
    });
});

$router->get('/books/get/{method:[a-z]+}[/{id:[0-9]+}]', 'BooksController@get_books');
$router->get('/books/get[/{id:[0-9]+}]', 'BooksController@get_books');

$router->get('/genres/get[/{id:[0-9]+}]', 'BooksController@get_genres');
$router->get('/authors/get[/{id:[0-9]+}]', 'BooksController@get_authors');



$router->group(['prefix' => 'books/', 'middleware' => 'auth:admin'], function () use ($router) {
    $router->post('add', 'BooksController@add_book');
    $router->delete('/drop/{id:[0-9]+}', 'BooksController@drop_book');
    $router->get('download[/{method}]', 'BooksController@CSV');

    $router->post('/add_genre/{id:[0-9]+}', 'BooksController@add_genre_to_book');
    $router->post('/add_author/{id:[0-9]+}', 'BooksController@add_author_to_book');

    $router->delete('/drop_genre/{id:[0-9]+}', 'BooksController@remove_genre_from_book');
    $router->delete('/drop_author/{id:[0-9]+}', 'BooksController@remove_author_from_book');
});

$router->group(['prefix' => 'genres/', 'middleware' => 'auth:admin'], function () use ($router) {
    $router->post('add', 'BooksController@add_genre');
    $router->delete('/drop/{id:[0-9]+}', 'BooksController@drop_genre');
});

$router->group(['prefix' => 'authors/', 'middleware' => 'auth:admin'], function () use ($router) {
    $router->post('add', 'BooksController@add_author');
    $router->delete('/drop/{id:[0-9]+}', 'BooksController@drop_author');
});

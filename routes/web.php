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

$router->get('/', function () use ($router) {
    return "Test";
});

$router->group(['prefix' => 'users/'], function () use ($router) {
    $router->post('registry', 'UsersController@registry');
    $router->group(['prefix' => '{id:[0-9]+}/'], function () use ($router) {
        $router->post('add_to_favorite', 'UsersController@add_fav_book');
        $router->delete('remove_from_favorite', 'UsersController@rem_fav_book');
    });
});

$router->get('/books[/{id:[0-9]+}]', 'BooksController@get_books');

$router->group(['prefix' => 'books/'], function () use ($router) {
    $router->post('add', 'BooksController@add_book');
    $router->get('download[/{method}]', 'BooksController@CSV');
    $router->delete('/{id:[0-9]+}/drop', 'BooksController@drop_book');
});



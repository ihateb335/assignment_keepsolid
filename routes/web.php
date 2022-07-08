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
$router->post('/users/registry', 'UsersController@registry');
$router->post('/users/{id}/add_to_favorite', 'UsersController@add_fav_book');
$router->post('/users/{id}/remove_from_favorite', 'UsersController@rem_fav_book');

$router->post('/books/add', 'BooksController@add_book');
$router->delete('/books/drop', 'BooksController@drop_book');
$router->get('/books[/{id:int}]', 'BooksController@get_books');

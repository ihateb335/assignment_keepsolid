<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookshelf extends Model
{
    protected $table = 'bookshelf';
    protected $connection = 'user';
    public $timestamps = false;
}

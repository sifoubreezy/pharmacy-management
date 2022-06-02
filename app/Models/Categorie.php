<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Post;

class Categorie extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';

    public function post()
    {
        return $this->hasOne(Post::class, 'categorie_id', 'id');
    }
}

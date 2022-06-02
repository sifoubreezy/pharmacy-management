<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Post;

class Form extends Model
{
    protected $table = 'forms';
    protected $primaryKey = 'id';

    public function post()
    {
        return $this->hasOne(Post::class, 'form_id','id');
    }
}

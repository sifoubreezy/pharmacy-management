<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'content', 'user_id', 'post_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->select(['id','name']);
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }
}

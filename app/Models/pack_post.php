<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Comments;
use App\Models\Form;
use App\Models\Categorie;
class pack_post extends Model
{
    protected $table = 'packs_posts';
    protected $primaryKey = 'id';

    public function post()
    {
        return $this->belongsTo(Post::class,'post_id','id');
    }

    public function packs(){
        return $this->belongsTo(Pack::class,'pack_id','id');
    }

}

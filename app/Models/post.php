<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Comments;
use App\Models\Form;
use App\Models\Categorie;
class Post extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id';
    protected $date_perm;
    protected $dates=['date_perm'] ;
    protected $date = ['creation_date'];


    /**
     * @var date|null
     */
    public function comments()
    {
        return $this->hasMany(Comments::class, 'post_id', 'id');
    }

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id', 'id');
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pack_post()
    {
        return $this->hasMany(pack_post::class,'pack_id','id');
    }
}

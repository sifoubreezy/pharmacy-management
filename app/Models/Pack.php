<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Comments;
use App\Models\Form;
use App\Models\Categorie;
class Pack extends Model
{
    protected $table = 'packs';
    protected $primaryKey = 'id';

    public function packPost()
    {
        return $this->hasMany(pack_post::class,'pack_id','id');
    }




}

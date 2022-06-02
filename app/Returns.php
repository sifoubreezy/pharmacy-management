<?php

namespace App;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;

class Returns extends Model
{
    public function returnContents()
    {
        return $this->hasMany(ReturnContent::class, 'return_id', 'id');
    }
}

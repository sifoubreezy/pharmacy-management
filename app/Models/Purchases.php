<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Purchases extends Model
{
    protected $table = 'purchases';
    protected $primaryKey = 'id';

    public function purchaseContents()
    {
        return $this->hasMany(PurchaseContent::class, 'purchase_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function post(){
        return $this->belongsTo(Post::class,'post_id','id');
    }


}

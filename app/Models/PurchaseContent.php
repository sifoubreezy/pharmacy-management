<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseContent extends Model
{
    protected $table = 'purchase_content';
    protected $primaryKey = 'id';
    protected $dates=['creation'] ;


    public function purchase(){
        return $this->belongsTo(Purchases::class,'purchase_id','id');
    }


    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function pack()
    {
        return $this->belongsTo(Post::class, 'pack_id', 'id');
    }

}

<?php
/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 24/06/2018
 * Time: 00:55
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class CartContent extends Model
{
    /**
     * $table
     *
     * @var string
     */

    protected $table = 'cart_contents';
    protected $primaryKey = 'id';

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id','id');
    }

    public function pack()
    {
        return $this->belongsTo(Pack::class, 'pack_id','id');
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id','id');
    }


}
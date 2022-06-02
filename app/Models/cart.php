<?php
/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 24/06/2018
 * Time: 00:54
 */

namespace App\Models;


use App\User;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';
    protected $primaryKey = 'id';

    public function user()
    {
        return $this->hasOne(User::class, 'user_id', 'id');
    }

    public function cartContents()
    {
        return $this->hasMany(CartContent::class, 'cart_id', 'id')->with('post');
    }

}
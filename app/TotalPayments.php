<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class TotalPayments extends Model
{
    protected $table = 'total_payments';
    protected $primaryKey = 'id';
    protected $fillable=['rest',];

 public function user(){
     return $this->hasMany(User::class, 'user_id', 'id');
 }

}

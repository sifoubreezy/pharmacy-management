<?php

namespace App;
use App\TotalPayments;
use App\Models\Comments;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Cart;

class User extends Authenticatable
{
    protected $dates = ['created_at'];

     /**
     * @var date|null
     */
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role','user_id'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cart()
    {
        return $this->hasOne(Cart::class, 'user_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comments::class, 'user_id', 'id');
    }
    public function deposits(){

        return $this->hasMany(Deposit::class,'user_id','id');
    }
    public function total_paymetns(){
        return $this->belongsTo(TotalPayments::class, 'user_id', 'id');
    }

}

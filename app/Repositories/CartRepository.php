<?php

namespace App\Repositories;
use App\Models\Cart;

/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 27/06/2018
 * Time: 22:24
 */

class CartRepository extends CrudRepositoryImpl implements CrudRepository
{

    public function findCartWithCartContentsByUserId(int $userId): Cart
    {
        return Cart::with(['cartContents'])
            ->where('user_id', "=", $userId)->first();
    }

}
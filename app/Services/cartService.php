<?php

namespace App\Services;

use App\Repositories\CartRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 24/06/2018
 * Time: 01:24
 */
class CartService
{
    private $cartRepository;

    /**
     * CartService constructor.
     * @param $cartRepository
     */
    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }


    public function getCartInfo()
    {
        return $this->cartRepository->
            findCartWithCartContentsByUserId(Auth::user()->getAuthIdentifier());
    }
    public function getCartInfoById($id)
    { 
        return $this->cartRepository->
            findCartWithCartContentsByUserId($id);
    }

}
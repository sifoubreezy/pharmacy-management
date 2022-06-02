<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\CartContent;
use App\Repositories\CartContentRepository;

/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 24/06/2018
 * Time: 01:25
 */
class CartContentService
{
    private $cartContentRepository;
    private $postService;

    /**
     * CartContentService constructor.
     * @param $cartContentRepository
     */
    public function __construct(CartContentRepository $cartContentRepository, PostService $postService
    )
    {
        $this->cartContentRepository = $cartContentRepository;
        $this->postService = $postService;

    }


    public function deleteOne($id): void
    {
        $this->cartContentRepository->delete($id);
    }

    public function create($post_id, $cart_id,$qte)
    {
        $post=$this->postService->find($post_id);
        $unitPrice = $post->pv_ht;
                    if( Auth::user()->option == 4){
                        $unitPrice=$post->marge3;
                    }else if( Auth::user()->option == 3){
                        $unitPrice = $post->marge2;
                    }else if( Auth::user()->option == 2){
                        $unitPrice=$post->marge1;
                    }
        $cartContent = new CartContent();
        $cartContent->post_id = $post_id;
        $cartContent->cart_id = $cart_id;
        $cartContent->type = "post";
        $cartContent->qte = $qte;
        if($qte !==null){
        $cartContent->prix = $qte * $unitPrice;
    }else{
        $cartContent->prix =  $unitPrice;
    }
        $this->cartContentRepository->save($cartContent);
        return $cartContent;
    }
    function getCart($id){
        return CartContent::query()
        ->where('cart_contents.cart_id','=',$id)
        ->count();
    }
    function getPricee($id){
        return CartContent::query()
        ->where('cart_contents.cart_id','=',$id)
        ->sum('prix');
    }
}
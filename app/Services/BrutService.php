<?php

namespace App\Services;

use App\Models\PurchaseContent;
use App\Repositories\PurchasesRepository;
use App\Repositories\PostRepository;
use App\Repositories\UsersRepository;

use App\Brut;

/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 24/06/2018
 * Time: 01:25
 */
class BrutService
{
     private $postRepository; 
     private $usersRepository;
    private $UsersService;
    private  $fournisseurService;
    private $postService;
    private $purchasesRepository;

    /**
     * PurchasesService constructor.
     * @param PurchaseContentRepository $purchaseContentRepository
     * @param PurchasesRepository $purchasesRepository
     * @param PostService $postService
     * @internal param PurchasesRepository $purchasesRepository
     */
    public function __construct(
        PurchasesRepository $purchasesRepository,
        PostService $postService,
        UsersService $UsersService,
        fournisseurService $fournisseurService,
        usersRepository $usersRepository,
        postRepository $postRepository
    )
    {
        $this->postService = $postService;
        $this->purchasesRepository = $purchasesRepository;
        $this->fournisseurService = $fournisseurService;
        $this->UsersService = $UsersService;
        $this->usersRepository = $usersRepository;

    }

    
    public function createBrute(array $values)
    {
        $brut = new Brut();
        $post = $this->postService->find($values['post_id']);
       // $note = $values['note'];
       
        if (!!$post) {
            foreach ($values as $property => $value) { 
                if ($property === 'quantity') {
                    if ($value < $post->qte) {
                        $brut->$property = $value;
                        $post->qte = $post->qte - $value;
                    } else {
                        $brut->$property = $post->qte;
                        $post->qte = 0;
                    }

                } else { 
                    $brut->$property = $value;
                }
                
            }
           
            $brut->save();
            $this->postService->UpdatePost($post->id, [
                "qte" => $post->qte
            ]);
           

            return ["brut" => $brut, "post" => $post];
        } else {
            throw new \HttpException('Not Found', 404);
        }
    }
    public function getBondcommand(){
        return Brut::query()
            ->join('users', 'users.id', '=', 'bruts.user_id')           
            ->join('posts', 'posts.id', '=', 'bruts.post_id')           
             
            ->select('users.name','bruts.*')
            ->addSelect(DB::raw('posts.name as nom'))
            ->addSelect(DB::raw('users.name as nome'))
            
            ->orderBy('created_at', 'desc')
            
            ->get();
    }

    /*
    public function updatePurchase(int $id, array $values)
    {
        $purchaseContent = $this->purchaseContentRepository->find($id);
        $post = $this->postService->find($purchaseContent->post_id);
        $totalQte = $post->qte + $purchaseContent->quantity;

        foreach ($values as $property => $value) {
            if ($property === 'quantity') {
                if ($value < $totalQte) {
                    $purchaseContent->$property = $value;
                    $totalQte = $totalQte - $value;
                } else {
                    $purchaseContent->$property = $totalQte;
                    $totalQte = 0;
                }

            } else {
                $purchaseContent->$property = $value;
            }
        }
        $purchase = $this->purchasesRepository->find($purchaseContent->purchase_id);
        $purchase->total_price = $purchase->total_price - $purchaseContent->price;
        $purchaseContent->price = $purchaseContent->quantity * $purchaseContent->unit_price;
        $this->postService->UpdatePost($post->id, [
            "qte" => $totalQte
        ]);
        $purchaseContent = $this->purchaseContentRepository->save($purchaseContent);
        $purchase->total_price = $purchase->total_price + $purchaseContent->price;
        $this->purchasesRepository->save($purchase);

        return $purchaseContent;
    }

    public function deletePurchase(int $id)
    {
        $purchaseContent = $this->purchaseContentRepository->find($id);
        $purchase = $this->purchasesRepository->find($purchaseContent->purchase_id);
        $purchase->total_price = $purchase->total_price - $purchaseContent->price;
        $post = $this->postService->find($purchaseContent->post_id);
        $totalQte = $post->qte + $purchaseContent->quantity;
        $this->postService->UpdatePost($post->id, [
            "qte" => $totalQte
        ]);
        $return = $this->purchaseContentRepository->delete($id);
        $this->purchasesRepository->save($purchase);
        return $return;
    }
*/

}

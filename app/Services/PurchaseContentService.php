<?php

namespace App\Services;
use App\Models\Post;

use App\Models\PurchaseContent;
use App\Repositories\PurchaseContentRepository;
use App\Repositories\PurchasesRepository;
use App\Boncommand;
/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 24/06/2018
 * Time: 01:25
 */
class PurchaseContentService
{

    /**
     * @var PurchaseContentRepository
     */
    private $purchaseContentRepository;
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
        PurchaseContentRepository $purchaseContentRepository,
        PurchasesRepository $purchasesRepository,
        PostService $postService
    )
    {
        $this->purchaseContentRepository = $purchaseContentRepository;
        $this->postService = $postService;
        $this->purchasesRepository = $purchasesRepository;
    }

    public function createPurchase(array $values)
    {
        $purchaseContent = new PurchaseContent();
        $post = $this->postService->find($values['post_id']);
        if (!!$post) {
            foreach ($values as $property => $value) {
                if ($property === 'quantity') {
                    if ($value < $post->qte) {
                        $purchaseContent->$property = $value;
                        $post->qte = $post->qte - $value;
                    } else {
                        $purchaseContent->$property = $value;
                        $post->qte = $value;
                    }
 
                } else {
                    $purchaseContent->$property = $value;
                }
            }
            $purchaseContent->price = $purchaseContent->quantity * $purchaseContent->unit_price;
            $purchaseContent = $this->purchaseContentRepository->save($purchaseContent);
            $query= Post::query()->select("*")->where("nom_comr",'=',$post->nom_comr)
                        ->where('pv_ht','=',$post->pv_ht)
                        ->where('level','=',1)
                        ->where('qvip','=',null)
                        ->where('qimport','=',null)
                        ->where('qplusimport','=',null)
                        ->where('qord','=',null)
                        ->orderBy('date_perm','asc');
                    $posts=$query->get();
                    foreach ($posts as $index=>$post){


                        if ($index===0){

                            if ($post->qte>=$values['quantity']){

                                $this->postService->UpdatePost($post->id, [

                                    'qte' => $post->qte - $values['quantity'],

                                ]);


                            }else{
                                $this->postService->UpdatePost($post->id, [

                                    'qte' => 0,

                                ]);

                            }

                            $rest=$values['quantity']-$post->qte;
                        }else{
                            $rest -= $post->qte;
                            if($rest>0){
                                if ($post->qte>=$values['quantity']){

                                    $this->postService->UpdatePost($post->id, [

                                        'qte' => $post->qte - $values['quantity'],

                                    ]);
                                }else{
                                    $this->postService->UpdatePost($post->id, [

                                        'qte' => 0,

                                    ]);
                                }
                            }else{
                                $this->postService->UpdatePost($post->id, [

                                    'qte' =>  abs($rest),

                                ]);
                                break;
                            }

                        }




                    }
           /* $this->postService->UpdatePost($post->id, [
                "qte" => $post->qte
            ]);*/
            $purchase = $this->purchasesRepository->find($purchaseContent->purchase_id);
            $purchase->total_price = $purchase->total_price + $purchaseContent->price;
            $this->purchasesRepository->save($purchase);
            $this->deleteBonCommande($purchase->user_id,$purchaseContent->post_id);

            return ["purchase_content" => $purchaseContent, "post" => $post];
        } else {
            throw new \HttpException('Not Found', 404);
        }

    }

    public function deleteBonCommande($userId,$postId)
    { 
            try{
                $boncommands = Boncommand::query()
                ->where('user_id','=',$userId)
                ->where('post_id','=',$postId)
                ->first();
                if(!!$boncommands){
                    return $boncommands->delete();   

                }
            } catch(Exception | Throwable $e){
                // throw $e;
            }
     }

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
function getAll__($id){
    return PurchaseContent::query()
    ->where('purchase_content.user_id','=',$id)
    ->select(DB::raw('*'))
    ->get();
}

}

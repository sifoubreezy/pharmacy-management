<?php

namespace App\Services;

use App\Models\PurchaseContent;
use App\Repositories\PurchasesRepository;
use App\Repositories\PostRepository;
use App\Repositories\UsersRepository;
use Illuminate\Support\Facades\DB;

use App\Boncommand;

/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 24/06/2018
 * Time: 01:25
 */
class BoncommandService
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

    public function createBonCommand(array $values)
    {
        $boncommand = new Boncommand();
        $post = $this->postService->find($values['post_id']);
        if (!!$post) {
            foreach ($values as $property => $value) {
                    $boncommand->$property = $value;
            }
            $boncommand->saveOrFail();

            return ["boncommand" => $boncommand];
        } else {
            throw new \HttpException('Not Found', 404);
        }

    }
    public function getBondcommand(){
        return Boncommand::query()
            ->join('users', 'users.id', '=', 'boncommands.user_id')           
            ->join('posts', 'posts.id', '=', 'boncommands.post_id')           
            ->select(DB::raw('*'))

            ->select('boncommands.*')
            ->addSelect(DB::raw('posts.nom_comr as nom'))
            
            ->orderBy('created_at', 'desc')
            
            ->get();
    }
    public function getBon(){
        return Boncommand::query()
        ->join('posts', 'posts.id', '=', 'boncommands.post_id')           
        ->select(DB::raw('*'))

        ->whereNull('user_id')
        ->addSelect(DB::raw('posts.cover_image as img'))
        ->addSelect(DB::raw('posts.nom_comr as nom'))
        ->addSelect(DB::raw('boncommands.created_at as created'))
        ->addSelect(DB::raw('boncommands.id as idd'))

       // ->addSelect(DB::raw('users.name as nome'))
        
        ->orderBy('created', 'desc')
        
        ->get();
    }
    public function getBonCo(){
        $boncommands = Boncommand::select('*');
        return Boncommand::query()
            
            ->addSelect('checkbox', '<input type="checkbox" name="student_checkbox[]" class="student_checkbox" value="{{$id}}" />')
            ->select(DB::raw('checkbox'))
            //->make(true)
        
        //->orderBy('created', 'desc')
        
        ->get();
    }
    public function modifyQteById($id, int $qt): void
    {
        $amountBeforeSubmit = DB::table('boncommands')
            ->where('id', $id)->first();
        DB::table('boncommands')->where('id', $id)->update([
            'qt' => DB::raw($qt)
        ]);
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

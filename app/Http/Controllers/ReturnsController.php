<?php

namespace App\Http\Controllers;

use App\ReturnContent;
use App\Services\PostService;
use App\Services\PurchasesService;
use App\Services\ReturnContentsService;
use App\Services\ReturnsService;
use App\Services\NotificationsService;
use App\TotalPayments;
use Illuminate\Support\Facades\DB;
use App\Returns;
use App\User;
use App\Models\Post;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class ReturnsController extends Controller
{
    /**
     * @var PurchasesService
     */
    private $purchasesService;

    /**
     * @var ReturnsService
     */
    private $returnsService;
    /**
     * @var ReturnContentsService
     */
    private $returnContentsService;
    /**
     * @var PostService
     */
    private $postService;
    /**
     * @var NotificationsService
     */
    private $notificationsService;

    public function __construct(PostService $postService ,PurchasesService $purchasesService,ReturnContentsService $returnContentsService,ReturnsService $returnsService,NotificationsService $notificationsService)
    {
     $this->notificationsService=$notificationsService;
     $this->purchasesService=$purchasesService;
     $this->returnsService=$returnsService;
     $this->returnContentsService=$returnContentsService;
     $this->postService=$postService;

    }

    public function index()
    {
        if (Auth::user()->role!=='admin'||Auth::user()->role === 'Comptoir'){
        $returns=$this->returnsService->getAllByUserId(Auth::id());

        return View('claim/Return/index',["returns"=>$returns]);
    }else{
            return redirect()->back();
        }
    }
    public function create()
    {
        if (Auth::user()->role!=='admin'||Auth::user()->role === 'Comptoir'){

            $purchases = $this->purchasesService->getAllRecordsByUser(Auth::id());
            return View('claim/Return/create',['purchases' => $purchases]);
            
        }
        else{ 
            return redirect()->back();
        }
    }
    public function search(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $purchases = $this->purchasesService->searchByName($query,Auth::id());
            $output='<ul class="dropdown-menu text-center " style="display:block;position: absolute">';
            if (count($purchases)>0){
                foreach ($purchases as $purchase){
                    $output.='<li id="dropDownPurchase"><a data-id="'.$purchase->post_id.'" data-updated_at="'.$purchase->updated_at.'" data-qt="'.$purchase->quantity.'" href="#">'.$purchase->nom_comr.'</a></li>';
                }

            }
            else{
                $output.='<p class="text-center" style="padding: 2px 5px">Aucun Produit à Afficher.</p>';
            }
            $output.='</ul>';
            echo $output;

        }
    }
    public function searchByid(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $user_id = $request->get('user_id');
            $purchases = $this->purchasesService->searchByName($query,$user_id);
            $output='<ul class="dropdown-menu text-center " style="display:block;position: absolute;left: 0!important;max-height: 200px;overflow-y:auto">';
            if (count($purchases)>0){
                foreach ($purchases as $purchase){
                    $output.='<li id="dropDownPurchase"><a data-id="'.$purchase->post_id.'" data-updated_at="'.$purchase->updated_at.'" data-qt="'.$purchase->quantity.'" href="#">'.$purchase->nom_comr.'</a></li>';
                }

            }
            else{
                $output.='<p class="text-center" style="padding: 2px 5px">Aucun Produit à Afficher.</p>';
            }
            $output.='</ul>';
            echo $output;

        }
    }
    public function store(Request $request){

        try {
            DB::beginTransaction();

           // $user=User::where('id',$request->get('user_id'))->first();
            //$returne = new Returns();
            //$returne->user_id=$user->id;
            $inputs = $request->input('data');
            $total=0;
            $unitPrice=0;
            foreach ($inputs as $input){
                $post=$this->postService->find($input['postId']);
                $quantity=$input['quantity'];
               // $unitPrice = $post->pv_ht;
                if( Auth::user()->option == 4){
                    $unitPrice=$post->marge3;
                }else if( Auth::user()->option == 3){
                    $unitPrice = $post->marge2;
                }else if( Auth::user()->option == 2){
                    $unitPrice=$post->marge1;
                }
                $total+=$unitPrice*$quantity;

            }

          /*  if (DB::table('total_payments')->where('user_id', $user->id)->count() > 0) {
                DB::table('total_payments')->where('user_id', $user->id)->update([
                    'total_amount' => DB::raw('total_amount - '. (float)$total .''),
                ]);
                DB::table('total_payments')->where('user_id', $user->id)->update([
                    'rest' => DB::raw('rest - '. (float)$total .''),
                ]);
            }*/

            foreach ($inputs as $input) {
                $purchaseQuantity=$this->purchasesService->findQuantityByPostId($input['postId']);
                $quantity=$input['quantity'];

                if ($quantity>$purchaseQuantity ){

                    throw new Exception('invalid quantity');
                }else{
                  $returne =  $this->returnsService->create( Auth::id(),$total);
                    $returnContent=new ReturnContent();
                    $returnContent->return_id=$returne->id;
                    $returnContent->post_id=$input['postId'];
                    $returnContent->quantity=$quantity;
                    $returnContent->save();

                }
                $this->notificationsService->createNotification(Auth::user()->name.'retour de produit ','bon-retour');

            }
            DB::commit();

        } catch (Exception | Throwable $e) {

            throw $e;
        }
    }
    public function storeForAdmin(Request $request){

        try {
            DB::beginTransaction();

            $user=User::where('id',$request->get('user_id'))->first();
           // $returne = new Returns();
            //$returne->user_id=$user->id;
            $inputs = $request->input('data');
            $total=0;
            foreach ($inputs as $input){

                $quantity=$input['quantity'];
                $total+=(float)$this->postService->find($input['postId'])->pv_ht*$quantity;
                

              // $this->notificationsService->createNotification(Auth::user()->name.'retour de produit ','bon-retour');
               $post = Post::query()->where('id','=',$input['postId'])->first();

                $this->postService->UpdatePost($post->id, [
                    'qte' => $post->qte + $input['quantity'],

                ]);

            }
            $return=$this->returnsService->create($request->get('user_id'),$total);
            foreach ($inputs as $input){

                $quantity=$input['quantity'];
                     $returnContent=new ReturnContent();
                    $returnContent->return_id=$return->id;
                    $returnContent->post_id=$input['postId'];
                    $returnContent->quantity=($quantity);
                    $returnContent->save();
            }
            DB::table('total_payments')->where('id','=',$request->get('user_id'))->update(
                [
                    'rest' => DB::raw('rest - '. (float)$total .''),
                ]

            );

            DB::commit();
            
            


        } catch (Exception | Throwable $e) {

            throw $e;
        }
    }
    public function confirm(Request $request){
        if (Auth::user()->role==='admin'||Auth::user()->role === 'Comptoir') {
            $returnId = $request->get('returnId');
            $returns=Returns::find($returnId)->toArray();
            $return = ReturnContent::query()->where('return_id', '=', $returnId)->first();
            $return->confirmed = true;
            DB::table('total_payments')->where('id','=',$returns['user_id'])->update(
                [
                    'rest' => DB::raw('rest - '. (float)$returns['total'] .''),
                ]

            );
            $post = Post::find($return->toArray()['post_id']);

            $this->postService->UpdatePost($post->id, [
                'qte' => $post->qte +$return->toArray()['quantity'],


            ]);
            $return->save();
        }
    }
    public function ReturnContent($id)
    {
        if (Auth::user()->role !== 'admin'||Auth::user()->role === 'Comptoir') {
        $returns=$this->returnContentsService->getContentById($id);
        return View('claim.Return.ReturnContent',['returns'=>$returns]);
    }else{
            return redirect()->back();
        }
    }
}

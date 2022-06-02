<?php

namespace App\Http\Controllers;
use App\Models\PurchaseContent;

use App\Models\Post;
use App\Models\Purchases;

use App\Services\CartContentService;
use App\Services\CartService;
use App\Services\NotificationsService;
use App\Services\PacksService;
use App\Services\PostService;
use App\Services\PurchaseContentService;
use App\Services\PurchasesService;
use App\Services\TotalPaymentsService;
use App\TotalPayments;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class PurchasesController extends Controller
{
    /**
     * @var PurchasesService
     */
    private $purchasesService;
    /**
     * @var PostService
     */
    private $postService;
    /**
     * @var PurchaseContentService
     */
    private $purchaseContentService;
    /**
     * @var TotalPaymentsService
     */
    private $totalPaymentsService;
    /**
     * @var CartService
     */
    private $cartService;
    /**
     * @var CartContentService
     */
    private $cartContentService;
    /**
     * @var NotificationsService
     */
    private $notificationsService;
    /**
     * @var PacksService
     */
    private $packsService;

    /**
     * PurchasesController extends constructor.
     *
     * @param PacksService           $packsService
     * @param TotalPaymentsService   $totalPaymentsService
     * @param PurchasesService       $purchasesService
     * @param PostService            $postService
     * @param PurchaseContentService $purchaseContentService
     * @param CartService            $cartService
     * @param CartContentService     $cartContentService
     * @param NotificationsService   $notificationsService
     */
    public function __construct(PacksService $packsService, TotalPaymentsService $totalPaymentsService, PurchasesService $purchasesService, PostService $postService, PurchaseContentService $purchaseContentService, CartService $cartService, CartContentService $cartContentService, NotificationsService $notificationsService)
    {
        $this->middleware('auth');
        $this->purchasesService = $purchasesService;
        $this->postService = $postService;
        $this->purchaseContentService = $purchaseContentService;
        $this->cartService = $cartService;
        $this->cartContentService = $cartContentService;
        $this->notificationsService = $notificationsService;
        $this->packsService = $packsService;
        $this->totalPaymentsService = $totalPaymentsService;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return bool
     *
     * @throws Throwable
     */
    public function create(Request $request)
    {
        try { 
            DB::beginTransaction();
            $inputs = $request->input('data');
            $ids=[];
            foreach($inputs as $input){
                array_push($ids,$input['postId']);
            }
            $purchasesss =  PurchaseContent::query()
            ->join('posts','posts.id','=','purchase_content.post_id')
            ->join('purchases','purchases.id','=','purchase_content.purchase_id')
            ->select(DB::raw('*'))
            ->where('purchases.user_id','=',Auth::id())
            ->where('purchase_content.post_id','=',$ids)
            ->where('posts.qvip','!=',null)
            ->where('posts.qplusimport','!=',null)
            ->where('posts.qimport','!=',null)
            ->where('posts.qord','!=',null)
            ->get();
            if($purchasesss -> count()>0){
                throw new Exception('invalid product');

            }else{
                $purchase = $this->purchasesService->createPurchase(['user_id' => Auth::id()]);
                $payment_method = $request->input('payment_method');
                $purchase->payment_method = intval($payment_method);
                    
                $purchase->save();
                $totalPrice = 0;
                $totalNet=0;
                foreach ($inputs as $input) {
                    if ($input['type'] === 'post') {
                        $post = $this->postService->find($input['postId']);
    //                    if ($post->qte < $input['quantity']) {
    //                        throw new Exception('invalid quantity');
    //                    }
    
                        $unitPrice = $post->pv_ht;
    
                        if( Auth::user()->option == 4){
                            $unitPrice=$post->marge3;
                        }else if( Auth::user()->option == 3){
                            $unitPrice = $post->marge2;
                        }else if( Auth::user()->option == 2){
                            $unitPrice=$post->marge1;
                        }
     
                        $purchaseContent = $this->purchaseContentService->createPurchase([
                            'purchase_id' => $purchase->id,
                            'post_id' => $post->id,
                            'quantity' => $input['quantity'],
                            'unit_price'=>  $unitPrice,
                            'price' => (float)($unitPrice * $input['quantity']),
                            'type' => 'post',
                        ]);
                        $totalPrice += (float)$purchaseContent['purchase_content']->price;
                        $totalNet=$totalPrice;
                        $query= Post::query()->select("*")->where("nom_comr",'=',$post->nom_comr)
                            ->where('pv_ht','=',$post->pv_ht)
                            ->where('date_perm','=',$post->date_perm)
                            ->where('level','=',1)
                            ->where('qvip','=',null)
                            ->where('qimport','=',null)
                            ->where('qplusimport','=',null)
                            ->where('qord','=',null)
                            ->orderBy('date_perm','asc');
                        $posts=$query->get();
                        foreach ($posts as $index=>$post){
    
    
                            if ($index===0){
    
                                if ($post->qte>=$input['quantity']){
    
                                    $this->postService->UpdatePost($post->id, [
    
                                        'qte' => $post->qte - $input['quantity'],
    
                                    ]);
    
    
                                }else{
                                    $this->postService->UpdatePost($post->id, [
    
                                        'qte' => 0,
    
                                    ]);
    
                                }
    
                                $rest=$input['quantity']-$post->qte;
                            }else{
                                $rest -= $post->qte;
                                if($rest>0){
                                    if ($post->qte>=$input['quantity']){
    
                                        $this->postService->UpdatePost($post->id, [
    
                                            'qte' => $post->qte - $input['quantity'],
    
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
                                break;
    
                            }
    
    
    
    
                        }
    
    //                    $this->postService->UpdatePost($post->id, [
    //                        'qte' => $post->qte - $input['quantity'],
    //                        'sold' => $post->sold + $input['quantity'],
    //                    ]);
                    } else {
                        $pack = $this->packsService->findPackById($input['packId']);
    
                        $purchaseContent = $this->purchaseContentService->createPurchase([
                            'purchase_id' => $purchase->id,
                            'pack_id' => $pack->id,
                            'unit_price' => $pack->price,
                            'price' => $pack->price,
                            'quantity' => 1,
                            'type' => 'pack',
                            'payment_method' => (int)$payment_method,
                        ]);
                        $totalPrice += (float)$purchaseContent->price;
                        $totalNet=$totalPrice;
                    }
                }
    
    
                $this->purchasesService->UpdatePurchase($purchase->id, ['total_price' => $totalPrice]);
                $this->purchasesService->UpdatePurchaseNet($purchase->id, ['total_net' => $totalNet]);
    
                if (DB::table('total_payments')->where('user_id', Auth::id())->count() > 0) {
                    DB::table('total_payments')->where('user_id', Auth::id())->update([
                        'total_amount' => DB::raw('total_amount + '.floatval($totalPrice).''),
                    ]); 
                    DB::table('total_payments')->where('user_id', Auth::id())->update([
                        'rest' => DB::raw('rest + '. (float)$totalPrice .''),
                    ]);
                } else {
                    $total_payments = new TotalPayments();
                    $total_payments->user_id = Auth::id();
                    $total_payments->total_amount = (float)$totalPrice;
                    $total_payments->rest = (float)$totalPrice;
                    $total_payments->save();
                }
                if (DB::table('users')->where('id', Auth::id())->count() > 0) {
                    DB::table('users')->where('id', Auth::id())->update([
                        'rese' => DB::raw('rese + '.floatval($totalPrice).''),
                    ]);  
                } else {
                    $user = new User();
                    $user->id = Auth::id();
                    $user->rese = (float)$totalPrice;
                    $user->save();
                }
     
                foreach ($this->cartService->getCartInfo()->cartContents as $content) {
                    $this->cartContentService->deleteOne($content->id);
                }
    
    
                if((float)TotalPayments::query()->where('user_id', '=', Auth::id())->first()->rest > (float)User::query()->where('id','=',Auth::id())->first()->credit)
                {
                    $this->notificationsService->createNotification(Auth::user()->name.' depasse','credit-depasse');
                }
    //
                $this->notificationsService->createNotification(Auth::user()->name.' vient de confirmer ses achats','bon-de-commande');
    
            }

                       DB::commit();
        } catch (Exception | Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function index()
    {
        if (Auth::user()->role === 'Comptoir'|| Auth::user()->level == 6) {
            return redirect()->back()->withErrors('Vous n\'êtes pas autorisé pour acceder cette page !!!');
        } else {
            $purchases = $this->purchasesService->findAllByUser(Auth::id());
            $total = $this->totalPaymentsService->getTotal(Auth::id());
            $rest = $this->totalPaymentsService->getRest(Auth::id());

            return view('purchases.index', ['purchases' => $purchases, 'total' => $total, 'rest' => $rest]);
        }
    }

    public function find(int $id, Response $response)
    {
        $purchase = $this->purchasesService->find($id);
        if ($purchase !== null) {
            if (Auth::id() === intval($purchase->user_id, 10) || Auth::user()->role === 'admin' || Auth::user()->level == 5) {
                return view('purchases.purchase_content', ['purchase' => $purchase]);
            } else {
                $response->setStatusCode(401, 'unauthorized');

                return redirect('/')->withErrors('Unauthorized');
            }
        } else {
            $response->setStatusCode(404, 'Not Found');

            return redirect('/')->withErrors('Not Found');
        }
    }

    public function marqueAsSeen(Request $request)
    {
        if (Auth::user()->role === 'admin'|| Auth::user()->level == 6) {
            $purchase = $this->purchasesService->find($request->input('id'));
            $purchase->seen = true;
            $purchase->save();

            return redirect('/purchases')->with('success', 'La commande a été marquée comme \'Vu\'');
        } else {
            return redirect()->back();
        }
    }
    public function deletePurchaseById($id)
    {
       $purchase=PurchaseContent::find($id);
       $purchase->delete($id);
    }
    public function glaa(Request $request)
    {

        $id=$request->get('id');
        $total_price= (float)$request->get('total_price');
        $user_id= $request->get('user_id');
        $this->purchasesService->modifyDeposittById($id,$total_price,$user_id);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\CartContent;
use App\Models\Post;
use App\Services\CartContentService;
use App\Services\CartService;
use App\Services\PacksService;
use App\Services\PostService;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class CartController extends Controller
{
    private $cartService;
    private $cartContentService;


    public function __construct(CartService $cartService, CartContentService $cartContentService)
    {
        $this->middleware('auth');
        $this->cartContentService = $cartContentService;
        $this->cartService = $cartService;

    }

    public function index(Response $response)
    {
        if (Auth::user()->role !== 'admin') {

            $cart = $this->cartService->getCartInfo();
            $allUsers=User::select(array('name','id'))
                ->where('valide','=',1)
                ->where('role','!=','Comptoir')
                ->where('role','!=','admin')->orWhereNull('role')
                ->get();

            if ($cart->cartContents->count()>0){
                $totalQtes=[];
            foreach ($cart->cartContents as $i=>$cartContent){
                $post=Post::query()->where('id','=',$cartContent->post_id)->first();
                $totalQte= Post::query()->select(DB::raw('sum(qte) as sumQte'))
                       ->where("nom_comr",'=',$post->nom_comr)
                       ->where('pv_ht','=',$post->pv_ht)
                       ->where('level','=',1)
                       ->where('qvip','=',null)
                       ->where('qimport','=',null)
                       ->where('qplusimport','=',null)
                       ->where('qord','=',null)
                      // ->groupBy('date_perm')                       

                        ->first();
                array_push($totalQtes,$totalQte);
            }
                //var_dump(json_encode($totalQtes));

                //die();
            return view('cart.index', ['cart' => $cart,'role'=>Auth::user()->role,'allUsers'=>$allUsers,'totalQte'=>$totalQtes]);
            }else{
                return view('cart.index', ['cart' => $cart,'role'=>Auth::user()->role,'allUsers'=>$allUsers]);
            }
        } else {
            $response->setStatusCode(403);

            return redirect('/');
        } 
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return $this
     */
    public function delete(Request $request, Response $response)
    {
        try {
            $this->cartContentService->deleteOne($request->input('id'));

            return redirect()->route('cart')->with('success', 'L\'élément a été supprimé !!!');
        } catch (Exception | Throwable $e) {
            $response->setStatusCode(406, 'une erreur s\'est produite');

            return redirect()->route('cart')->withErrors('une erreur s\'est produite');
        }
    }

    public function create(Request $request)
    {

        if ($request->get('userId')){
            $userId=$request->get('userId');
            $inputs = $request->input('data');

            foreach ($inputs as $input){
 
                try {
                    if($input['type'] === 'post')
                    {
                        $cart = $this->cartService->getCartInfoById($userId);
                        $cartContent = new CartContent();
                        $cartContent->type = "post";
                        $cartContent->post_id = $input['postId'];
                        $cartContent->cart_id = $cart->id;
                        $cartContent->qte = $input['qte'];
                        $cartContent->save();
                        return redirect('/cart')->with('info', 'L\'élément est déjà présent dans le panier');

                    }else {
                        $cart = $this->cartService->getCartInfoById($userId);
                        $cartContent = new CartContent();
                        $cartContent->type = "pack";
                        $cartContent->pack_id = $input['packId'];
                        $cartContent->cart_id = $cart->id;
                        $cartContent->save();
                    }

                }catch (Exception $e){

                }
            }


        } 
        else{
        try {
            if($request->input('type') === 'pack')
            {
                $cart = $this->cartService->getCartInfo();
                $cartContent = new CartContent();
                $cartContent->type = "pack";
                $cartContent->pack_id = $request->input('id');
                $cartContent->cart_id = $cart->id;
                $cartContent->save();
                return redirect('/Packs')->with('success', 'L\'élément a été ajouté');
            }else {
                $cart = $this->cartService->getCartInfo();
                $this->cartContentService->create($request->input('id'), $cart->id,$request->input('qte'));
                return redirect('/home')->with('success', 'L\'élément a été ajouté');
            }
        } catch (Exception $e) {
            return redirect('/cart')->with('info', 'L\'élément est déjà présent dans le panier');
        }
        }
    }



}

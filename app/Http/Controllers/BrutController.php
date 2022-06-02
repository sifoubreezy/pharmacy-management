<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Brut;

use App\Models\post;
use App\Services\BrutService;

class BrutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $brutService;

    public function __construct(BrutService $brutService){
        $this->brutService = $brutService;

    }

    public function index()
    {
        $bruts = 
        Brut::query()
        ->join('posts','posts.id', '=','bruts.post_id')
        ->orderBy('bruts.created_at', 'desc')
    
        ->get();

        return view('Brut.index')->with('bruts', $bruts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try{
            DB::beginTransaction();
            $quantity = $request->input('quantity');
            $post_id = $request->input('post_id');
            //$purchase_id = $request->input('purchase_id');
            $note = $request->input('note','defqult');
            $user_id = $request->input('user_id');
            
            $values = [
                "quantity" => $quantity,
                "post_id" => $post_id,
               // "purchase_id" => $purchase_id,
               // "user_id"=> $user_id,
            
              "note"=>$note
            ];
           $bruts=$this->brutService->createBrute($values);
        

           DB::commit();
           return $bruts;
       } catch (\Exception $e) {
           DB::rollBack();
           throw $e;
       }
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        foreach ($postsInBruts as $post){
            $bruts= new Brut();
            $bruts->quantity = $post->qte;
            $bruts->post_id = $post->id;
            $bruts->user_id = $user->id;
            $bruts->note = $post->note;
            $bruts->save();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    public function createProduitBrut(Request $request)
    {
        if (Auth::user()->role === "admin"||Auth::user()->role === 'Comptoir') {
            try {
                DB::beginTransaction();
                $quantity = $request->input('quantity');
                $note = $request->input('note');
                $post_id = $request->input('post_id');
                
                $user_id = $request->input('user_id');
                $values = [
                    "quantity" => $quantity,
                    "post_id" => $post_id,
                    "note" => $note,
                    "user_id"=> $user_id
                    
                ];

                $return = $this->brutService->createBrut($values);
                DB::commit();
                return $return;
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } else {
            throw new \HttpException('Forbidden', 403);
        }
    }
    public function addProductToCommande()
    {
        DB::beginTransaction();
        $inputs = $request->input('data');
            foreach ($inputs as $input) {
                if ($input['type'] === 'post') {
                    $post = $this->postService->find($input['postId']);
                    if ($post->qte < $input['quantity']) {
                        throw new Exception('invalid quantity');
                    }
                    $bruts = $this->brutService->createPurchase([
                        'post_id' => $post->id,
                        'quantity' => $input['quantity'],
                        /*if(Auth::user()->option === 4){
                            'unit_price' => $post->marge3;
                        }*/
                        'note' => $input['note'],
                        'type' => 'post',
                    ]);
           }}
    }
}

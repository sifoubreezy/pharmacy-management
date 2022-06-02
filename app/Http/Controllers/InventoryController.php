<?php

namespace App\Http\Controllers;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use App\Models\Post;
use App\Invoice;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * @var PostService
     */
    private $postService;

    public function __construct(PostService $postService)
    {
        $this->postService=$postService;

    }

    public function index()
    {

        if (Cookie::get("inventory_data")){
            $cookie=json_decode(Cookie::get("inventory_data"));
            return view('inventory.index',['cookie'=>$cookie]);
        }else{
            $posts=$this->postService->getLastPostsInvent(99999999);
            return view('inventory.index',['Posts'=>$posts]);
        }

 


    }
    public function annee()
    {

        
            $posts=$this->postService->getLastPostsInvent(99999999);
            return view('inventory.anne',['Posts'=>$posts]);


 


    }
    public function modifyAnnee(Request $request)
    {
        if( $request->get('action')==='craser'){
            $posts = Post::query()->select('*')->where('id',$request->get('postId'))->get();

            DB::table('posts')->where('id',$request->get('postId') )->update([
                'inventory' => DB::raw('inventory + ' . $request->get('qte') . '')
            ]);
            $inviocie=new Invoice();
            $inviocie->ref_invoice_id =1;
            $inviocie->date_perm='';
            $inviocie->ppa=1;
            $inviocie->pv_ht=1;
            $inviocie->com_name='';
            $inviocie->image=''; 
            $inviocie->quantity=$request->get('qte');
            $inviocie->user_id=Auth::user()->id;
            $inviocie->post_id=$request->get('postId');
$inviocie->save();
           /* foreach($posts as $post){
                DB::table('posts')->where('nom_comr','=',$post->nom_comr )
                ->where('pv_ht','=',$post->pv_ht )
                ->where('date_perm','=',$post->date_perm )
            ->update([
                'invontair' => DB::raw('invontair + ' . $request->get('qte') . '')
            ]);
            }*/
        }else{
            DB::table('posts')->where('id',$request->get('postId') )->update([
                'qte' => DB::raw('qte + ' . $request->get('qte') . '')
            ]);
        }
    }

    public function modify(Request $request)
    {
        if( $request->get('action')==='craser'){
            $posts = Post::query()->select('*')->where('id',$request->get('postId'))->get();

            DB::table('posts')->where('id',$request->get('postId') )->update([
                'qte' => DB::raw( $request->get('qte') )
            ]);
            foreach($posts as $post){
                DB::table('posts')->where('nom_comr','=',$post->nom_comr )
                ->where('pv_ht','=',$post->pv_ht )
                ->where('date_perm','=',$post->date_perm )
            ->update([
                'qte' => DB::raw( $request->get('qte') )
            ]);
            }
        }else{
            DB::table('posts')->where('id',$request->get('postId') )->update([
                'qte' => DB::raw('qte + ' . $request->get('qte') . '')
            ]);
        }
    }
    public function resetInvent(Request $request){

        //$inputs = $request->input('products');
        $inputs=Post::query()
        ->select('*')
        ->where('ref_invoice_id','!=',null)
        //->where('inventory','!=',null)
        ->get();
        foreach ($inputs as $input){

            try {

                DB::table('posts')->where('id',$input->id )->update([
                    'qte' => DB::raw( 0 )
                ]);



            }catch (\Exception $exception){


            }
        }

        Cookie::queue( Cookie::forget('inventory_data'));

    }
    public function reset(Request $request){

        $inputs = $request->input('products');

        foreach ($inputs as $input){

            try {

                DB::table('posts')->where('id',$input['postId'] )->update([
                    'qte' => DB::raw( 0 )
                ]);



            }catch (\Exception $exception){


            }
        }

        Cookie::queue( Cookie::forget('inventory_data'));

    }
    public function save(Request $request){
        $data = $request->input('products');
        if (Cookie::get("inventory_data")){
            Cookie::forget('inventory_data');
        }
        Cookie::queue(Cookie::make('inventory_data', json_encode($data),0.001));
    }
    public function invent(){
        $datass=Post::query()->select('*')
        ->where('date_perm','!=',null)
        ->where('qte','!=',null)
        ->where('prix','!=',null)

        ->get();
        foreach($datass as $datas){
            DB::table('invoices')->insert([
            'post_id' => $datas->id, 
            'ppa'=>$datas->qte, 
            'date_perm'=>$datas->date_perm, 
            'ref_invoice_id'=>0,
            'image'=>0,
            'com_name'=>$datas->nom_comr,
            'pv_ht'=>$datas->prix,
            'quantity'=>0,

            ]);
        }
    }
    public function svaeInvent(){
        $posts=Invoice::query()
        ->select('*')
        ->addSelect(DB::raw('SUM(quantity) as total'))
        ->where('user_id','!=',null)
        //->where('inventory','!=',null)
        ->groupBy('post_id')
        ->get();
        foreach($posts as $post){
            DB::table('posts')->where('id','=',$post->post_id )->update([
                'qte' => DB::raw( $post->total )
            ]); 
        }
        Invoice::query()->delete();
        return redirect('/inventory/anne')
        ->with('success',"saved successfuly");
    }
     public function getInventoryProduct(Request $request)
    {
       $invoices= Invoice::query()
        ->join('users','users.id','=','invoices.user_id')
        ->join('posts','posts.id','=','invoices.post_id')
        ->select('invoices.quantity','posts.nom_comr','users.name')
        ->where('post_id','=',$request->get('postId'))
        ->get();
        return response()->json(['data' => $invoices]);
 
    }
    
}

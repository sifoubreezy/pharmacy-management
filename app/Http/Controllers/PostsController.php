<?php
namespace App\Http\Controllers;

use App\Models\
{
    Categorie, Form, post
};
use App\Services\
{
    CategorieService, CommentsService, PacksService, PostService
};
use Exception;
use App\Invoice;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\Facades\Image;
use Throwable;
use Excel;

class PostsController extends Controller
{
    public $postService;
    /**
     * @var CommentsService
     */
    private $commentsService;

    private $categorieService;
    /**
     * @var PacksService
     */
    private $packsService;

    /**
     * Create a new controller instance.
     *
     * @param PacksService $packsService
     * @param PostService $postService
     * @param CommentsService $commentsService
     * @param CategorieService $categorieService
     */
    public function __construct(PostService $postService, CommentsService $commentsService, CategorieService $categorieService)
    {
        $this->middleware('auth', ['except' => ['index', 'show', 'getImage', 'theme']]);

        $this->postService = $postService;
        $this->categorieService = $categorieService;
        $this->commentsService = $commentsService;

    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Factory|Application|View
     */
    public function index(Request $request)
    {
        $query = $request->query('query', null);
        $api = $request->query('api', null);
        $categorieQuery = $request->query('categorie', null);
        $theme = $request->cookie('theme');

        if ($query === null)
        {
            $posts = $this
                ->postService
                ->getLastPosts(12);
        }
        else
        {

            $posts = $this
                ->postService
                ->getSearchedPosts(12, $query);
        }
        if ($categorieQuery !== null)
        {
            $posts = $this
                ->postService
                ->searchByCategorie($categorieQuery, 12);
        }
        $categories = $this
            ->categorieService
            ->getCategorie();

        return view('Posts.index')
            ->with(['Posts' => $posts, 'categories' => $categories, 'theme' => $theme]);
    }

    /**
     * Change the display mode of products.
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function theme(Request $request)
    {
        $minutes = 1000000000000;
        return redirect('/Posts')->cookie('theme', $request->input('theme') , $minutes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Response $response
     *
     * @return Factory|Application|View
     */
    public function create(Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir')
        {
            $list = Form::all()->pluck('form', 'id');
            $list2 = Categorie::all()->pluck('categorie', 'id');

            return view('Posts.create')
                ->with(['list' => $list, 'list2' => $list2]);
        }
        else
        {
            $response->setStatusCode(403, 'unauthorized');

            return redirect('/')
                ->withErrors('Unauthorized');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Response                 $response
     *
     * @return Application|RedirectResponse|Redirector
     */

    public function store(Request $request, Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir')
        {
            $this->validate($request, ['nom_comr' => 'required', 'CTherapeutique' => 'nullable', 'nom_dci' => 'nullable', 'cover_image' => 'image|nullable|max:1999', 'prix' => 'required', 'qte' => 'required', 'date_perm' => 'required', 'tag' => 'nullable','remise' => 'nullable',
 
            ]);

            if ($request->hasFile('cover_image'))
            {
                $fileNameWithText = $request->file('cover_image')
                    ->getClientOriginalName();
                $filename = pathinfo($fileNameWithText, PATHINFO_FILENAME);
                $extension = $request->file('cover_image')
                    ->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $image = Image::make($request->file('cover_image'));
                $image->resize(400, 300);
               // $image->stream();
                //$fileNameWithText->move($path,$fileNameToStore['cover_image'], (string)$image->encode());

                Storage::put('images\\' . $fileNameToStore, (string)$image->encode());
              /*  $fileNameWithText = $request->file('cover_image');
                $fileNameToStore=time().'.'.$fileNameWithText->getClientOriginalExtension();
                $path=public_path('images\cover_image');
                $image = Image::make($request->file('cover_image'));
                $image->resize(400,300);
                //$image->stream();
                //Storage::put('images\\' . $fileNameToStore, (string)$image->encode());
                //$image->save($path.$fileNameToStore);

                $fileNameWithText->move($path,$fileNameToStore, (string)$image->encode());*/

            }
            else
            {
                $fileNameToStore = 'noimage.jpg';
            }

            $this
                ->postService
                ->createPost(['nom_comr' => $request->input('nom_comr') ,
                    'CTherapeutique' => $request->input('CTherapeutique') ,
                    'qte' => $request->input('qte') ,
                    'prix' => $request->input('prix') ,
                    'nom_dci' => $request->input('nom_dci') ,
                    'date_perm' => $request->input('date_perm') , 
                    'cover_image' => $fileNameToStore, 
                    'tag' => $request->input('tag') , 
                    'Conditionnement' => $request->input('Conditionnement') , 
                    'dosage' => $request->input('dosage') , 
                    'type' => $request->input('type') , 
                    'form_id' => $request->input('form') ,
                     'categorie_id' => $request->input('categorie') ,
                      'pv_ht' => $request->input('pv_ht') , 
                      'offre' => $request->input('offre') , 
                      'level' => $request->input('level') ,
                      'qvip'=> $request->input('qvip'),
                      'qplusimport'=> $request->input('qplusimport'),
                      'qimport'=> $request->input('qimport'),
                      'qord'=> $request->input('qord'), ]);

            if ($request->has('from'))
            {
                return redirect('/Posts')
                    ->with('success', 'Annonce créé'); # code...

            }
            else
            {
                $data = array(
                    'success' => "Annonce créé",
                    'image' => $fileNameToStore,
                );
                return response()->json(['data' => $data]);
            }

        }
        else
        {
            $response->setStatusCode(403, 'unauthorized');

            return redirect('/')
                ->withErrors('Unauthorized');
        }
    }
    public function storeFromInvoice(Request $request, Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir')
        {
           $this->validate($request, ['date_perm' => 'required']);

            if ($request->hasFile('cover_image'))
            {
                $fileNameWithText = $request->file('cover_image')
                    ->getClientOriginalName();
                $filename = pathinfo($fileNameWithText, PATHINFO_FILENAME);
                $extension = $request->file('cover_image')
                    ->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $image = Image::make($request->file('cover_image'));
                $image->resize(400, 300);
                Storage::put('images\\' . $fileNameToStore, (string)$image->encode());
            }
            else
            {
                $fileNameToStore = 'noimage.jpg';
            }
$ss = $request->input('CTherapeutique');
$dd = intval($ss);
           $post= $this
                ->postService
                ->createPostFromInvoice(['nom_comr' => $request->input('nom_comr') ,

                    'qte' => $request->input('qte') + $dd ,
                    'prix' => $request->input('prix') ,
                    'nom_dci' => $request->input('qtec') ,
                    'date_perm' => $request->input('date_perm') ,
                    'cover_image' => $fileNameToStore,
                    'tag' => $request->input('tag') ,
                    'Conditionnement' => $request->input('Conditionnement') ,
                    'dosage' => $request->input('emplacement') ,
                    'categorie_id' => $request->input('categorie') ,
                    'pv_ht' => $request->input('pv_ht') ,
                    'offre' => $request->input('offre') ,
                    'sold' => $request->input('ppa') ,
                    'CTherapeutique' => $request->input('CTherapeutique') ,
                    'type' => $request->input('type') ,
                    'inventory'=>0,
                    'marge1' => $request->input('marge_1') ,
                    'marge2' => $request->input('marge_2') ,
                    'marge3' => $request->input('marge_3') ,
                    'remise' => $request->input('discountItem') ,
                    'level' => $request->input('level') ,
                    'qvip'=> $request->input('qvip'),
                      'qplusimport'=> $request->input('qplusimport'),
                      'qimport'=> $request->input('qimport'),
                      'qord'=> $request->input('qord'), ]);

            if ($request->has('from'))
            {
                return redirect('/Posts')
                    ->with('success', 'Annonce créé'); # code...

            }

            $data = array(
                'success' => "Annonce créé avec ->default('value');",
                'image' => $fileNameToStore,
                'post_id'=>$post->id
            );
            return response()->json(['data' => $data]);

        }
        else
        {
            $response->setStatusCode(403, 'unauthorized');

            return redirect('/')
                ->withErrors('Unauthorized');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int      $id
     * @param Response $response
     *
     * @return Factory|Application|View
     */
    public function show(int $id, Response $response)
    {
        try
        {
            $posts = $this
                ->postService
                ->findPost($id);
            $comments = $this
                ->commentsService
                ->findAll($id);

            return view('Posts.show', ['Posts' => $posts, 'comments' => $comments, ]);
        }
        catch(Exception | Throwable $e)
        {
            $response->setStatusCode(404, 'Cette announce n\'existe plus');

            return redirect('/Posts')
                ->withErrors('Cette announce n\'existe plus');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int      $id
     * @param Response $response
     *
     * @return Factory|Application|View
     */
    public function edit($id, Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir')
        {
            $list = Form::all()->pluck('form', 'id');
            $list2 = Categorie::all()->pluck('categorie', 'id');
            $level = DB::table('posts')->where('id', $id)->value('level');
            $nom_comr = DB::table('posts')->where('id', $id)->value('nom_comr');
            $nom_dci = DB::table('posts')->where('id', $id)->value('nom_dci');
            $dosage = DB::table('posts')->where('id', $id)->value('dosage');
            $cond = DB::table('posts')->where('id', $id)->value('Conditionnement');
            $date_perm = DB::table('posts')->where('id', $id)->value('date_perm');
            $pv_ht = DB::table('posts')->where('id', $id)->value('pv_ht');
            $prix = DB::table('posts')->where('id', $id)->value('prix');
            $qte = DB::table('posts')->where('id', $id)->value('qte');
            $offre = DB::table('posts')->where('id', $id)->value('offre');
            $tag = DB::table('posts')->where('id', $id)->value('tag');
            $egets = DB::table('posts')->where('id', $id)->value('CTherapeutique');
            $marge1 = DB::table('posts')->where('id', $id)->value('marge1');
            $marge2 = DB::table('posts')->where('id', $id)->value('marge2');
            $marge3 = DB::table('posts')->where('id', $id)->value('marge3');
            $sold = DB::table('posts')->where('id', $id)->value('sold');
            $type = DB::table('posts')->where('id', $id)->value('type');
            $remise = DB::table('posts')->where('id', $id)->value('remise');



            try
            {
                $posts = $this
                    ->postService
                    ->findPost($id);

                return view('Posts.edit', ['Posts' => $posts, 'list' => $list, 'list2' => $list2, 'level' => $level, 'nom_comr' => $nom_comr, 'nom_dci' => $nom_dci, 'dosage' => $dosage, 'cond' => $cond, 'date_perm' => $date_perm, 'pv_ht' => $pv_ht, 'prix' => $prix, 'qte' => $qte, 'offre' => $offre, 'tag' => $tag,'marge3' => $marge3,'marge2' => $marge2,'marge1' => $marge1,'sold' => $sold,'type' => $type,'remise' => $remise,'egets' => $egets, ]);
            }
            catch(Exception | Throwable $e)
            {
                return redirect('/Posts')->withErrors('Cette announce n\'existe plus');
            }
        }
        else
        {
            $response->setStatusCode(403, 'unauthorized');

            return redirect('/')
                ->withErrors('Unauthorized');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int                      $id
     * @param Response                 $response
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function update(Request $request, $id, Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir')
        {
            $this->validate($request, ['nom_comr' => 'required', 
            'CTherapeutique' => 'required', 
            'nom_dci' => 'required', 
            'cover_image' => 'image|nullable|max:1999', 
            'prix' => 'required', 
            'qte' => 'required', 
            'date_perm' => 'required', 
            'tag' => 'nullable', 
            'type' => 'nullable', 
            'dosage' => 'required',
             'Conditionnement' => 'required', 
             'form_id' => 'nullable', 
             'pv_ht' => 'required', ]);

            if ($request->hasFile('cover_image'))
            {
                $fileNameWithText = $request->file('cover_image')
                    ->getClientOriginalName();
                $filename = pathinfo($fileNameWithText, PATHINFO_FILENAME);
                $extension = $request->file('cover_image')
                    ->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $image = Image::make($request->file('cover_image'));
                $image->resize(400, 300);
                Storage::put('images\\' . $fileNameToStore, (string)$image->encode());
            }
            else
            {
                $fileNameToStore = 'noimage.jpg';
            }

            try
            {
                $post = $this
                    ->postService
                    ->UpdatePost($id, ['nom_comr' => $request->input('nom_comr') , 
                    'CTherapeutique' => $request->input('CTherapeutique') ,
                     'qte' => $request->input('qte') , 
                     'prix' => $request->input('prix') , 
                     'nom_dci' => $request->input('nom_dci') ,
                      'date_perm' => $request->input('date_perm') , 
                      'cover_image' => $fileNameToStore, 
                      'tag' => $request->input('tag') , 
                      'Conditionnement' => $request->input('Conditionnement') , 
                      'dosage' => $request->input('dosage') , 
                      'type' => $request->input('type') , 
                      'form_id' => $request->input('form') , 
                      'categorie_id' => $request->input('categorie') , 
                      'pv_ht' => $request->input('pv_ht') , 
                      'offre' => $request->input('offre') , 
                      'level' => $request->input('level') , 
                      'marge1' => $request->input('marge1') , 
                      'marge2' => $request->input('marge2') , 
                      'marge3' => $request->input('marge3') , 
                      'sold' => $request->input('sold') , 
                      'remise' => $request->input('remise') , 
                      

                      ]);

                return redirect("/Posts/$post->id")
                    ->with('success', 'Anonnce modifiée');
            }
            catch(Exception | Throwable $e)
            {
                return redirect('/Posts')->withErrors('une erreur s\'est produite');
            }
        }
        else
        {
            $response->setStatusCode(403, 'unauthorized');

            return redirect('/')
                ->withErrors('Unauthorized');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int      $id
     * @param Response $response
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function destroy($id, Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir')
        {
            try
            {
                $this
                    ->postService
                    ->deletePost($id);

                return redirect('/Posts')->with('success', 'Annonce supprimée');
            }
            catch(Exception | Throwable $e)
            {
                throw $e;

                return redirect('/Posts')->withErrors('une erreur s\'est produite');
            }
        }
        else
        {
            $response->setStatusCode(403, 'unauthorized');

            return redirect('/')
                ->withErrors('Unauthorized');
        }
    }
    public function delete($id, Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir')
        {
            try
            {
                $this
                    ->postService
                    ->deletePost($id);
                   // $response->setStatusCode(204, 'no content');
                   return redirect('/Posts')->withErrors('une erreur s\'est produite');

                //return;
            }
            catch(Exception | Throwable $e)
            {
                throw $e;

                return redirect('/Posts')->withErrors('une erreur s\'est produite');
            }
        }
        else
        {
            $response->setStatusCode(403, 'unauthorized');

            return;
        }
    }

    public function outdatedPosts(Response $response, Request $request)
    {
        $posts = $this
            ->postService
            ->date();
        $categorieQuery = $request->query('categorie', null);
        if ($categorieQuery !== null)
        {
            $posts = $this
                ->postService
                ->searchByCategorie($categorieQuery, 12);
        }
        $categories = $this
            ->categorieService 
            ->getCategorie();

        return view('Posts.outdatedPosts', ['Posts' => $posts, 'categories' => $categories]);
    }

    public function getImage($name)
    {
        return response(Storage::get('images\\' . $name) , 200)->header('Content-Type', 'image/jpeg');
    }
    public function endProduct(){
        $posts = Post::where('ref_invoice_id','!=',0)

        ->orderBy('nom_comr', 'desc')->get();
        return view('Posts.endProduct')->with('posts', $posts);

    }
    public function getDDP(Response $response,Request $request){
        
            $fromDate = $request->input('from_date','');
            $to_date = $request->input('to_date','');
            $posts = $this
            ->postService
            ->date();

            if(strlen($fromDate)>0 && strlen($to_date)>0){
                $gains = $this->postService->findAllPostsWhereDatePerm($fromDate,$to_date);
                return view('Posts.endDate', ['gains' => $gains,'Posts' => $posts, 'role' => Auth::user()->role]);
            }
            if(strlen($fromDate)>0 && !strlen($to_date)>0){
                $gains = $this->postService->findAllPosts($fromDate);
                return view('Posts.endDate', ['gains' => $gains,'Posts' => $posts, 'role' => Auth::user()->role]);
            }
            
            if(!strlen($fromDate)>0 && strlen($to_date)>0){
                $gains = $this->postService->findAllPostsWhere($to_date);
                return view('Posts.endDate', ['gains' => $gains,'Posts' => $posts, 'role' => Auth::user()->role]);
            }
            if( !strlen($fromDate)>0 && !strlen($to_date)>0){
                $gains = $this->postService->findAllPostsDDP();
               
                return view('Posts.endDate', ['gains' => $gains,'Posts' => $posts, 'role' => Auth::user()->role]);
            }
        
        
   }

    public function getAllqte(Request $request)
    {
        $post=Post::query()->where('id','=',$request->get('id'))->first();
        $qte= Post::query()->select("*")->where("nom_comr",'=',$request->get('name'))
            ->where('pv_ht','=',$post->pv_ht)
            ->where('ref_invoice_id','!=',0)

            ->where('level','=',1)
            ->where('qvip','=',null)
            ->where('qimport','=',null)
            ->where('qplusimport','=',null)
            ->where('qord','=',null)
            ->addSelect(DB::raw('sum(qte) as sumQte'))
            ->get()->toArray()[0]['sumQte'];
        echo $qte;
} 
function countPost(Request $request,Response $response){
   // $posts=Post::query()->where('nom_comr','=',$request->get('name'))->first();
    return Post::query()->select('*')
                        ->addSelect(DB::raw('sum(qte) as sumQte'))
                    ->where("nom_comr",'=',$request->get('name'))
                    ->where('ref_invoice_id','!=',0)

                    //->where('pv_ht','=',$posts->pv_ht)
                    ->groupBy('pv_ht')
                    //->groupBy('nom_comr')                       
                       //->addSelect(DB::raw('sum(qte) as sumQte'))
                        ->get();
                      //return  response()->json($post);
                        

}
public function hideProduct(Request $request)
    {
        $post = Post::find($request->get('id'));
        $post->level = $request->get('level');

        $post->save();

        return redirect('/Posts/product')->with('success', 'L\'inscription de '.$post->nom_comr.' a été confirmé');
    }
    public function productAll(Request $request)
    {
        $post = Post::query()
        ->select(DB::raw('*'))
        ->where('ref_invoice_id','!=',0)

        //->where('invoices.id','!=',0)
        ->get();
        return view('/Posts.product', ['post' => $post]);
    }
    function import(Request $request)
    {
     $this->validate($request, [
      'select_file'  => 'required|mimes:xls,xlsx'
     ]);

     $path = $request->file('select_file')->getRealPath();

     $data = Excel::load($path)->get();

     if($data->count() > 0)
     {
      foreach(($data->toArray())[0] as $key => $value)
      {
       // set_time_limit(500);
       //foreach($value as $row)
       //{
           
        $insert_data = array(
         'nom_comr'  => (isset($value['nom_comr']) ? $value['nom_comr'] : ''),
         'qte'   => (isset($value['qte']) ? $value['qte'] : ''),
         'marge1'  => (isset($value['marge1']) ? $value['marge1'] : ''),
         'prix'   => (isset($value['prix']) ? $value['prix'] : ''),
         'pv_ht'    => (isset($value['pv_ht']) ? $value['pv_ht'] : ''),
         'dosage'   => (isset($value['dosage']) ? $value['dosage'] : ''),
         'nom_dci'   => (isset($value['nom_dci']) ? $value['nom_dci'] : ''),
         'type'   => (isset($value['type']) ? $value['type'] : ''),
         'categorie_id'   => 1,
         'level'   => 1,
         'cover_image'=>'noimage.jpg',
         'created_at'=>new \DateTime(),
         'updated_at'=>new \DateTime(),
        );
       //}
       if(!empty($insert_data))
      {
       DB::table('posts')->insert($insert_data);
      }
      }
      
     }
     return back()->with('success', 'Excel Data Imported successfully.');
    }
    public function updateAttr(Request $request)
    {
        Post::where('id', '=', $request->get('id'))->update([$request->get('type') => $request->get('value')]);
        echo 'success';
        
             
    }
    public function updatepostfrominv(int $id,Request $request){
        $post = $id;
        $qte=$request->get('qte');
        $pv_ht = $request->get('pv_ht');
        Post::where('id', '=', $post)->update(['qte'=>$qte,'pv_ht'=>$pv_ht]);
        echo 'success';
    }
    function productqte_(){
        $productqte= Post::query()
        ->select(DB::raw('*'))
        ->addSelect(DB::raw('sum(qte) as sumQte'))
        ->where('qte','!=',0)
        ->where('ref_invoice_id','!=',0)
        ->groupBy('posts.pv_ht','posts.date_perm')
        ->orderBy('posts.date_perm', 'desc')
        ->get();
        return view('/Posts.productAll', ['productqte' => $productqte]);
                
    }
    function getNew(Request $request){
        
            $posts = $this
                ->postService
                ->getLastPostsnew(12);
       

        return view('/Brut.new')
            ->with(['Posts' => $posts]);
    }
    function getpromo(Request $request){
        
        $posts = $this
            ->postService
            ->getLastPostspromo(12);
   

    return view('/Posts.promo')
        ->with(['Posts' => $posts]);
}
public function tag(Request $request)
    {
        $post = Post::find($request->get('id'));
        $post->tag = $request->get('tag');

        $post->save();

        return redirect('/Posts/product')->with('success', 'La\'modification de '.$post->nom_comr.' a été confirmé');
    }

}


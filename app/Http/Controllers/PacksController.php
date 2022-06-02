<?php

namespace App\Http\Controllers;


use App\Models\Pack;
use App\Models\pack_post;
use App\Models\Post;
use App\Services\PacksService;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;


class PacksController extends Controller
{
    /**
     * @var PacksService
     */
    private $packsService;
    /**
     * @var PostService
     */
    private $postService;

    /**
     * PacksController constructor.
     * @param PacksService $packsService
     * @param PostService $postService
     */
    public function __construct(PacksService $packsService, PostService $postService)
    {
        $this->middleware('auth', ['except' => ['index','show']]);

        $this->packsService = $packsService;
        $this->postService = $postService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $packs = $this->packsService->getLastPacks(12);
        return view('Packs.index')->with(['packs'=>$packs]);
    }




    /**
     * Show the form for creating a new resource.
     *
     * @param Response $response
     *
     * @return Response
     */
    public function create(Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
            $posts = $this->postService->findPosts("");
            $posts = $posts->pluck("nom_comr",'id');
            return view('Packs.create')->with(compact('posts'));
        } else {
            $response->setStatusCode(403, 'unauthorized');

            return redirect('/')->withErrors('Unauthorized');
        }
    }



    public function store(Request $request,Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
            $postsInPack = [];
            $i=0;
            $totalPrice=0;
            foreach ($request->input('medicaments') as $medicament){
                $post=Post::find($medicament);
                $post->qte=$request->input('qtes')[$i];
                $post->type=$request->input('posts_type')[$i];
                $totalPrice+=$post->prix * $post->qte;
                array_push($postsInPack,$post);
                $i++;
            }
            if ($request->hasFile('cover_image')) {
                $fileNameWithText = $request->file('cover_image')->getClientOriginalName();
                $filename = pathinfo($fileNameWithText, PATHINFO_FILENAME);
                $extension = $request->file('cover_image')->getClientOriginalExtension();
                $fileNameToStore = $filename.'_'.time().'.'.$extension;
                $image = Image::make($request->file('cover_image'));
                $image->resize(400, 300);
                Storage::put('images\\'.$fileNameToStore, (string) $image->encode());
            } else {
                $fileNameToStore = 'packtemp.jpg';
            }

            $pack = new Pack();
            $pack->description=$request->input('description');
            $pack->cover_image=$fileNameToStore;
            $pack->price = $totalPrice;
            $pack->save();

            foreach ($postsInPack as $post){
                $packPost= new pack_post();
                $packPost->post_qte = $post->qte;
                $packPost->post_id = $post->id;
                $packPost->pack_id = $pack->id;
                $packPost->type = $post->type;
                $packPost->save();
            }
            return redirect('/Packs')->with('success', 'Annonce créé');
        } else {
            $response->setStatusCode(403, 'unauthorized');

            return redirect('/')->withErrors('Unauthorized');
        }
    }

    public function update(Request $request,Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
            $id=intval($request->input('id'),10);
            $postsInPack = [];
            $i=0;
            $totalPrice=0;
            foreach ($request->input('medicaments') as $medicament){
                $post=Post::find($medicament);
                $post->qte=$request->input('qtes')[$i];
                $post->type=$request->input('posts_type')[$i];
                $totalPrice+=$post->prix * $post->qte;
                array_push($postsInPack,$post);
                $i++;
            }
            if ($request->hasFile('cover_image')) {
                $fileNameWithText = $request->file('cover_image')->getClientOriginalName();
                $filename = pathinfo($fileNameWithText, PATHINFO_FILENAME);
                $extension = $request->file('cover_image')->getClientOriginalExtension();
                $fileNameToStore = $filename.'_'.time().'.'.$extension;
                $image = Image::make($request->file('cover_image'));
                $image->resize(400, 300);
                Storage::put('images\\'.$fileNameToStore, (string) $image->encode());
            } else {
                $fileNameToStore = null;
            }

            $pack = $this->packsService->findPackById($id);
            $pack->description=$request->input('description');
            $pack->cover_image=$fileNameToStore??$pack->cover_image;
            $pack->price = $totalPrice;
            $pack->save();

            foreach ($postsInPack as $post){
                $packPosts=$this->packsService->findPackPostByPackId($pack->id);
                foreach ( $packPosts as $packPost){
                    if($post->id ==  $packPost->post_id){
                        $packPost->post_qte = $post->qte;
                        $packPost->type = $post->type;
                        $packPost->save();
                        $postsInPack = array_filter($postsInPack,function(Post $elm)use ($post){
                            return $elm->id !== $post->id;
                        });
                        break;
                    }
                }
            }

            foreach ($postsInPack as $post){
                    $packPost= new pack_post();
                    $packPost->post_qte = $post->qte;
                    $packPost->post_id = $post->id;
                    $packPost->pack_id = $pack->id;
                    $packPost->type = $post->type;
                    $packPost->save();
            }
            return redirect('/Packs')->with('success', 'Annonce créé');
        } else {
            $response->setStatusCode(403, 'unauthorized');

            return redirect('/')->withErrors('Unauthorized');
        }
    }


    public function delete(Request $request, Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
            Pack::destroy([$request->input('id')]);
            return redirect('/Packs')->with('success', 'Annonce Supprimer');
        } else {
            $response->setStatusCode(403, 'unauthorized');

            return redirect('/')->withErrors('Unauthorized');
        }
    }

    public function deletePostFromPack(Request $request, Response $response,int $packId,int $packPostId)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
            $this->packsService->dropPackPost($packPostId);
            return response()->json(['msg'=>'success','content'=> 'Le Medicament a ete retirer de votre pack']);
        } else {
            $response->setStatusCode(403, 'unauthorized');

            return redirect('/')->withErrors('Unauthorized');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param int      $id
     * @param Response $response
     *
     * @return Response
     */
    public function show(int $id, Response $response)
    {
        try {
            $pack = $this->packsService->findPackById($id);

            return view('Packs.show')->with([
                'pack' => $pack
            ]);
        } catch (Exception | Throwable $e) {
            $response->setStatusCode(404, 'Cette announce n\'existe plus');

            return redirect('/Packs')->withErrors('Cette announce n\'existe plus');
        }
    }

    public function edit(Response $response,int $id)
    {

        try {
            $pack = $this->packsService->findPackById($id);
            $posts = $this->postService->findPosts("");
            $posts = $posts->pluck("nom_comr",'id');
            return view('Packs.edit')->with(compact("pack","posts"));
        } catch (Exception | Throwable $e) {
            $response->setStatusCode(404, 'Cette announce n\'existe plus');

            return redirect('/Packs')->withErrors('Cette announce n\'existe plus');
        }
    }

}

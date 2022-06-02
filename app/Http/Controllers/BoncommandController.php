<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Boncommand;
use App\Fournisseur;
use App\Models\post;
use App\Services\BoncommandService;

class BoncommandController extends Controller
{

    private $boncommandService;

    public function __construct(BoncommandService $boncommandService){
        $this->boncommandService = $boncommandService;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     */
    
    public function index($userId)
    {
        $boncommands = 
        Boncommand::orderBy('name', 'desc')
        ->join('posts', 'posts.id', '=', 'boncommands.post_id')
        ->select(DB::raw('*'))

        ->where('user_id','=',$userId)
        ->addSelect(DB::raw('boncommands.id as ids'))

        ->get();



        return view('Boncommand.bon')->with('boncommands', $boncommands);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fournisseurs = Fournisseur::all()->pluck('name', 'id');

        return view('Boncommand.create')->with(compact('fournisseurs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
            $this->validate($request, [
                'name' => 'required',
                'qt' => 'required',
            ]);
            $boncommand = new Boncommand();
            $boncommand->name = $request->input('name');
            $boncommand->qt = $request->input('qt');
            $boncommand->fournisseur_id = $request->get('fournisseur');
            $boncommand->save();
        }

        return redirect('/Boncommand')->with('succecess', 'Boncommand created');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    { 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir')
        {
            try
            {
                $boncommand = Boncommand::find($id);
                $boncommand->delete();   
                return redirect('/boncommand/{userId}')->with('success', 'Annonce supprimée');
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

    public function createPurchaseBoncommand(Request $request)
    {
        if (Auth::user()->role === "admin"||Auth::user()->role === 'Comptoir') {
            try {
                DB::beginTransaction();
                $quantity = $request->input('quantity');
                $post_id = $request->input('post_id');
                $purchase_id = $request->input('purchase_id');
                $fournisseur_id = $request->input('fournisseur_id');
                $user_id = $request->input('user_id');
                
                $values = [
                    "qt" => $quantity,
                    "post_id" => $post_id,
                    "purchase_id" => $purchase_id,
                    "user_id"=> $user_id,
                
                  "fournisseur_id"=>$fournisseur_id
                ];

                $return = $this->boncommandService->createBonCommand($values);
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

  
    public function find()
    {
        $boncommands = $this->boncommandService->getBondcommand();
       // $boncommands = $this->boncommandService->getBon();

        return view('Boncommand.find')->with('boncommands', $boncommands);
    }
    public function findAll()
    {
        //$boncommands = $this->boncommandService->getBondcommand();
        $fournisseurs = Fournisseur::all();

        $boncommands = $this->boncommandService->getBon();

        return view('Boncommand.all')->with('boncommands', $boncommands);
    }
    public function getDeletePost($id, Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir')
        {
            try
            {
                $boncommands = Boncommand::find($id);
                $boncommands->delete();   
                return redirect('/boncommand/{userId}')->with('success', 'Annonce supprimée');
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
    public function deleteAll(Request $request)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir')
        {
            try
            {
                $ids = $request->get('ids');
                $ids = DB::delete('delete from boncommands where id in('.implode(",", $ids).')');
                return redirect('/Boncommand/creta')->with('success', 'Annonce supprimée');

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
    public function modifyQteById(Request $request)
    {
        $id=$request->get('id');
        $qt= (int)$request->get('qt');
        
        $this->BoncommandService->modifyQteById($id,$qt);

    }

}

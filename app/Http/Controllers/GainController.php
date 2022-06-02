<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\Http\Request;
use App\Services\GainService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Services\BoncommandService;
use Illuminate\Support\Facades\Cookie;
use App\Fournisseur;


class GainController extends Controller
{
    private $gainService;
    private $boncommandService;
    private $postService;

    public function __construct(GainService $gainService, BoncommandService $boncommandService,PostService $postService)
    {
        $this->gainService = $gainService;
        $this->boncommandService = $boncommandService;
        $this->postService=$postService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Response $response,Request $request)
    {
        if (Auth::user() && Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
            $postName = $request->input('post-name','');
            $fromDate = $request->input('from_date','');
            $to_date = $request->input('to_date','');

            if(strlen($postName)>0 && strlen($fromDate)>0 && strlen($to_date)>0){
                $gains = $this->gainService->findGainsByPostAndCreatedAt($fromDate,$to_date,$postName);
                return view('gain.index', ['gains' => $gains, 'role' => Auth::user()->role]);
            }
            
            if(strlen($postName)>0 && strlen($fromDate)>0 && !strlen($to_date)>0){
                $gains = $this->gainService->findGainsByPostAndStartCreatedAt($fromDate,$postName);
                return view('gain.index', ['gains' => $gains, 'role' => Auth::user()->role]);
            }
            
            if(strlen($postName)>0 && !strlen($fromDate)>0 && strlen($to_date)>0){
                $gains = $this->gainService->findGainsByPostAndEndCreatedAt($to_date,$postName);
                return view('gain.index', ['gains' => $gains, 'role' => Auth::user()->role]);
            }
            
            if(strlen($postName)>0 && !strlen($fromDate)>0 && !strlen($to_date)>0){
                $gains = $this->gainService->findGainsByPost($postName);
                return view('gain.index', ['gains' => $gains, 'role' => Auth::user()->role]);
            }
            
            if(!strlen($postName)>0 && strlen($fromDate)>0 && strlen($to_date)>0){
                $gains = $this->gainService->findGainsByCreatedAt($fromDate,$to_date);
                return view('gain.index', ['gains' => $gains, 'role' => Auth::user()->role]);
            }
            
            if(!strlen($postName)>0 && !strlen($fromDate)>0 && strlen($to_date)>0){
                $gains = $this->gainService->findGainsByEndCreatedAt($to_date);
                return view('gain.index', ['gains' => $gains, 'role' => Auth::user()->role]);
            }
            
            if(!strlen($postName)>0 && strlen($fromDate)>0 && !strlen($to_date)>0){
                $gains = $this->gainService->findGainsByStartCreatedAt($fromDate);
                return view('gain.index', ['gains' => $gains, 'role' => Auth::user()->role]);
            }

            if(!strlen($postName)>0 && !strlen($fromDate)>0 && !strlen($to_date)>0){
                $gains = $this->gainService->getGains();
                $rests = $this->gainService->getRest();

                return view('gain.index', ['gains' => $gains,'rests'=>$rests, 'role' => Auth::user()->role]);
            }

        } else {
            $response->setStatusCode(403);

            return redirect('/');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
    public function destroy($id)
    {
    }
    public function creta(){
        $fournisseurs = Fournisseur::all();
        $gains = $this->gainService->getGains();
        $boncommands = $this->boncommandService->getBon();
        if (Cookie::get("inventory_data")){
            $cookie=json_decode(Cookie::get("inventory_data"));
            return view('Boncommand.creta',['cookie'=>$cookie,'gains' => $gains, 'boncommands' => $boncommands,'fournisseurs' => $fournisseurs]);
        }else{
            $posts=$this->postService->getLastPosts(12);
            return view('Boncommand.creta',['Posts'=>$posts,'gains' => $gains, 'boncommands' => $boncommands,'fournisseurs' => $fournisseurs]);
        }

    }
}

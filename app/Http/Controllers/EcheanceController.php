<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\EcheanceService;
use App\Services\PurchasesService;

class EcheanceController extends Controller
{
    private $echeanceService;
    private $purchasesService;
    public function __construct(EcheanceService $echeanceService,PurchasesService $purchasesService)
    {
        $this->middleware('auth');
        $this->echeanceService = $echeanceService;
        $this->purchasesService= $purchasesService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Response $response,Request $request)
    {
        if( !!Auth::user() &&  Auth::user()->role==='admin'||Auth::user()->role === 'Comptoir'){
            $echeances = $this->echeanceService->getGains();
            return view('echeances.index', ['echeances' => $echeances, 'role' => Auth::user()->role]);
        }else{

        } 
 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id ,Response $response,Request $request)
    {
        if( !!Auth::user() &&  Auth::user()->role==='admin'||Auth::user()->role === 'Comptoir'){
            $fromDate = $request->input('from_date',''); 
            $to_date = $request->input('to_date','');
         if(strlen($fromDate)>0 && strlen($to_date)>0){
                $echeances = $this->echeanceService->findEcheanceByUserID($id);
                $echs= $this->echeanceService->return($id);
                $eches= $this->echeanceService->Purchasesdate($id,$fromDate,$to_date);
                return view('echeances.show', ['echeances' => $echeances,'echs' => $echs,'eches' => $eches,'id'=>$id]);    
            }
        if(strlen($fromDate)>0 && !strlen($to_date)>0){
                $echeances = $this->echeanceService->findEcheanceByUserID($id);
                $echs= $this->echeanceService->return($id);
                $eches= $this->echeanceService->Purchasesfrom($id,$fromDate);
                return view('echeances.show', ['echeances' => $echeances,'echs' => $echs,'eches' => $eches,'id'=>$id,'role' => Auth::user()->role]);    
            }
        if(!strlen($fromDate)>0 && strlen($to_date)>0){
                $echeances = $this->echeanceService->findEcheanceByUserID($id);
                $echs= $this->echeanceService->return($id);
                $eches= $this->echeanceService->Purchasesto($id,$to_date);
                return view('echeances.show', ['echeances' => $echeances,'echs' => $echs,'eches' => $eches,'id'=>$id]);    
            }    
        if(!strlen($fromDate)>0 && !strlen($to_date)>0){
            $echeances = $this->echeanceService->findEcheanceByUserID($id);
            $echs= $this->echeanceService->return($id);
            $eches= $this->echeanceService->Purchases($id);
            return view('echeances.show', ['echeances' => $echeances,'echs' => $echs,'eches' => $eches,'id'=>$id]);    
        }
        }else{

        }
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
    public function filtre($id ,Response $response,Request $request)
    {
        if( !!Auth::user() &&  Auth::user()->role==='admin'||Auth::user()->role === 'Comptoir'){
            $fromDate = $request->input('from_date',''); 
            $to_date = $request->input('to_date','');
         if(strlen($fromDate)>0 && strlen($to_date)>0){
                $echeances = $this->echeanceService->findEcheanceByUserID($id);
                $rests = $this->echeanceService->getSoldee($id);

                $echs= $this->echeanceService->return($id);
                $eches= $this->echeanceService->Purchasesdate($id,$fromDate,$to_date);
                return view('echeances.filtre', ['echeances' => $echeances,'echs' => $echs,'eches' => $eches,'id'=>$id,'rests'=>$rests]);    
            }
        if(strlen($fromDate)>0 && !strlen($to_date)>0){
                $echeances = $this->echeanceService->findEcheanceByUserID($id);
                $rests = $this->echeanceService->getSoldee($id);

                $echs= $this->echeanceService->return($id);
                $eches= $this->echeanceService->Purchasesfrom($id,$fromDate);
                return view('echeances.filtre', ['echeances' => $echeances,'echs' => $echs,'eches' => $eches,'id'=>$id,'rests'=>$rests,'role' => Auth::user()->role]);    
            }
        if(!strlen($fromDate)>0 && strlen($to_date)>0){
                $echeances = $this->echeanceService->findEcheanceByUserID($id);
                $rests = $this->echeanceService->getSoldee($id);

                $echs= $this->echeanceService->return($id);
                $eches= $this->echeanceService->Purchasesto($id,$to_date);
                return view('echeances.filtre', ['echeances' => $echeances,'echs' => $echs,'eches' => $eches,'id'=>$id,'rests'=>$rests]);    
            }    
        if(!strlen($fromDate)>0 && !strlen($to_date)>0){
            $echeances = $this->echeanceService->findEcheanceByUserID($id);
            $rests = $this->echeanceService->getSoldee($id);

            $echs= $this->echeanceService->return($id);
            $eches= $this->echeanceService->Purchases($id);
            return view('echeances.filtre', ['echeances' => $echeances,'echs' => $echs,'eches' => $eches,'id'=>$id,'rests'=>$rests]);    
        }
        }else{

        }
    }
}

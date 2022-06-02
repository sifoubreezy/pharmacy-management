<?php

namespace App\Http\Controllers;
use App\Services\DepositServices;
use App\Services\ReturnsService;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\EtatService;



class EtatController extends Controller
{
    private $etatService;
    private $depositService;
    private $returnsService;

    public function __construct(EtatService $etatService,DepositServices $depositService,ReturnsService $returnsService)
    {
        $this->etatService = $etatService;
        $this->depositService=$depositService;
        $this->returnsService=$returnsService;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Response $response,Request $request)
    {

        if (Auth::user() && Auth::user()->role === 'admin') {
            $userName = $request->input('user-name','');
            $fromDate = $request->input('from_date',''); 
            $to_date = $request->input('to_date','');

            if(strlen($userName)>0 && strlen($fromDate)>0 && strlen($to_date)>0){
                $etats = $this->etatService->findGainsByPostAndCreatedAt($fromDate,$to_date,$userName);
                return view('etats.index', ['etats' => $etats, 'role' => Auth::user()->role]);
            }
            
            if(strlen($userName)>0 && strlen($fromDate)>0 && !strlen($to_date)>0){
                $etats = $this->etatService->findGainsByPostAndStartCreatedAt($fromDate,$userName);
                return view('etats.index', ['etats' => $etats, 'role' => Auth::user()->role]);
            }
            
            if(strlen($userName)>0 && !strlen($fromDate)>0 && strlen($to_date)>0){
                $etats = $this->etatService->findGainsByPostAndEndCreatedAt($to_date,$userName);
                return view('etats.index', ['etats' => $etats, 'role' => Auth::user()->role]);
            }
            
            if(strlen($userName)>0 && !strlen($fromDate)>0 && !strlen($to_date)>0){
                $etats = $this->etatService->findGainsByPost($userName);
                return view('etats.index', ['etats' => $etats, 'role' => Auth::user()->role]);
            }
            
            if(!strlen($userName)>0 && strlen($fromDate)>0 && strlen($to_date)>0){
                $etats = $this->etatService->findGainsByCreatedAt($fromDate,$to_date);
                return view('etats.index', ['etats' => $etats, 'role' => Auth::user()->role]);
            }
            
            if(!strlen($userName)>0 && !strlen($fromDate)>0 && strlen($to_date)>0){
                $etats = $this->etatService->findGainsByEndCreatedAt($to_date);
                return view('etats.index', ['etats' => $etats, 'role' => Auth::user()->role]);
            }
            
            if(!strlen($userName)>0 && strlen($fromDate)>0 && !strlen($to_date)>0){
                $etats = $this->etatService->findGainsByStartCreatedAt($fromDate);
                return view('etats.index', ['etats' => $etats, 'role' => Auth::user()->role]);
            }

            if(!strlen($userName)>0 && !strlen($fromDate)>0 && !strlen($to_date)>0){
                $etats = $this->etatService->getGains();
                
                return view('etats.index', ['etats' => $etats, 'role' => Auth::user()->role]);
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
    public function Etatfournisseur($id ,Response $response,Request $request){
        if( !!Auth::user() &&  Auth::user()->role==='admin'||Auth::user()->role === 'Comptoir'){
            $fromDate = $request->input('from_date',''); 
            $to_date = $request->input('to_date','');
         if(strlen($fromDate)>0 && strlen($to_date)>0){
                $echeances = $this->etatService->getProviderReturnToFrom($id,$fromDate,$to_date);
                $echs= $this->etatService->getProviderPaymentFromTo($id,$fromDate,$to_date);
                $eches= $this->etatService->getInvoicesFromTo($id,$fromDate,$to_date);
                return view('etats.show', ['echeances' => $echeances,'echs' => $echs,'eches' => $eches,'id'=>$id]);    
            }
        if(strlen($fromDate)>0 && !strlen($to_date)>0){
                $echeances = $this->etatService->getProviderReturnFrom($id,$fromDate);
                $echs= $this->etatService->getProviderPaymentFrom($id,$fromDate);
                $eches= $this->etatService->getInvoicesFrom($id,$fromDate);
                return view('etats.show', ['echeances' => $echeances,'echs' => $echs,'eches' => $eches,'id'=>$id,'role' => Auth::user()->role]);    
            }
        if(!strlen($fromDate)>0 && strlen($to_date)>0){
                $echeances = $this->etatService->getProviderReturnTo($id,$to_date);
                $echs= $this->etatService->getProviderPaymentTo($id,$to_date);
                $eches= $this->etatService->getInvoicesTo($id,$to_date);
                return view('etats.show', ['echeances' => $echeances,'echs' => $echs,'eches' => $eches,'id'=>$id]);    
            }    
        if(!strlen($fromDate)>0 && !strlen($to_date)>0){
            $echeances = $this->etatService->getProviderReturn($id);
            $echs= $this->etatService->getProviderPayment($id);
            $eches= $this->etatService->getInvoices($id);
            return view('etats.show', ['echeances' => $echeances,'echs' => $echs,'eches' => $eches,'id'=>$id]);    
        }
        }else{

        }
    }
    public function clientDetail(){
        $allDeposits=$this->depositService->findAllByUser(Auth::id());
        $returns=$this->returnsService->getAllByUserId(Auth::id());
        $purchase=$this->etatService->clientPurchase(Auth::id());
        return view('etats.clientDetail', ['allDeposits' => $allDeposits,'returns' => $returns,'purchase' => $purchase]);    
    }
}

<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use App\Services\TotalPaymentProviderService;
use App\Total_payments;
use App\TotalPayments;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TotalPaymentsController extends Controller
{

    private $totalPaymentProviderService;
public function __construct(TotalPaymentProviderService $totalPaymentProviderService)
{
    $this->totalPaymentProviderService=$totalPaymentProviderService;
}


    public function deletePaymentProvider($id)
    {


    }
    public function index(){
        $clients = TotalPayments::query()
        ->join('users','users.id','=','total_payments.user_id')
        ->select(DB::raw('*'))
        ->get();
        return view('sold.index')->with('clients', $clients);

    }
    public function edit($id)
    {
		$clients = TotalPayments::find($id);
		return view('sold.edit')->with('clients', $clients);
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
		if (Auth::user()->role === 'admin') {
            
            $clients = TotalPayments::find($id);
            $clients->rest = $request->input('rest');
            
            $clients->save();
        }

        return redirect('/sold')->with('succecess', 'solde updated');
    }


}

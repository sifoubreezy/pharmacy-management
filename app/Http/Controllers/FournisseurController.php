<?php

namespace App\Http\Controllers;

use App\Total_payment_provider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Fournisseur;

class FournisseurController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index()
    {
        $fournisseurs = Fournisseur::orderBy('name', 'desc')->get();

        return view('fourniseurs.index')->with('fournisseurs', $fournisseurs);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('fourniseurs.create');
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
            
            $fournisseur = new Fournisseur();
            $fournisseur->id;
            $fournisseur->name = $request->input('name');
            $fournisseur->adress = $request->input('adress');
            $fournisseur->ville = $request->input('ville');
            $fournisseur->email = $request->input('email');
            $fournisseur->telephone = $request->input('telephone');
            $fournisseur->save();
            $totalPayment=new Total_payment_provider();
            $totalPayment->provider_id=$fournisseur->id;
            $totalPayment->total_amount=0;
            $totalPayment->rest=0;
            $totalPayment->save();
        }

        return redirect('/fourniseurs')->with('succecess', 'fournisseur created');
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
        $fournisseur = Fournisseur::find($id);

        return view('fourniseurs.show')->with('fournisseur', $fournisseur);
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
		$fournisseur = Fournisseur::find($id);
		return view('fourniseurs.edit')->with('fournisseur', $fournisseur);
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
		if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
            $this->validate($request, [
                'name' => 'required',
                'adress' => 'required',
                'ville' => 'required',
                'email' => 'required',
                'telephone' => 'required',
            ]);
            $fournisseur = Fournisseur::find($id);
            $fournisseur->name = $request->input('name');
            $fournisseur->adress = $request->input('adress');
            $fournisseur->ville = $request->input('ville');
            $fournisseur->email = $request->input('email');
            $fournisseur->telephone = $request->input('telephone');
            $fournisseur->save();
        }

        return redirect('/fourniseurs')->with('succecess', 'fournisseur updated');
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
		            $fournisseur = Fournisseur::find($id);
                    $fournisseur->delete();
					return redirect('/fourniseurs')->with('succecess', 'fournisseur romoved');
    }
    public function providerEtat()
    {
        $fournisseurs = Fournisseur::orderBy('name', 'desc')->get();

        return view('fourniseurs.etat')->with('fournisseurs', $fournisseurs);
    }

}

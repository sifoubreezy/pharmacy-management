<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Services\CategorieService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{
    public function __construct(CategorieService $CategorieService)
    {
        $this->middleware('auth');
        $this->CategorieService = $CategorieService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Response $response)
    {
            if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
                $categories = $this->CategorieService->getCategoriesPaginated(12);

                return view('Categories.index')->with('categories', $categories);
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
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
            return view('Categories.create');
        }
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
        $this->validate($request, [
            'categorie' => 'required',
        ]);
        $categorie = new Categorie();
        $categorie->categorie = $request->input('categorie');
        $categorie->save();

        return redirect('/Categories');
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
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
            try {
                $categorie = Categorie::find($id);
                $categorie->delete();

                return redirect('/Categories')->with('success', 'Categorie supprimé');
            } catch (Exception | Throwable $e) {
                throw $e;

                return redirect('/Categories')->withErrors('une erreur s\'est produite');
            }
        }
    }
}

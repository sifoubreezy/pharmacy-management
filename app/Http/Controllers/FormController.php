<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Services\FormService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class FormController extends Controller
{
    private $formService;

    public function __construct(FormService $formService)
    {
        $this->middleware('auth');
        $this->formService = $formService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Response $response)
    {
            if (Auth::user()->role === 'admin') {
                $forms = $this->formService->getFormsPaginated(12);

                return view('Form.index')->with('forms', $forms);
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
        if (Auth::user()->role === 'admin') {
            return view('Form.create');
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
                'form' => 'required',
            ]);
        $form = new form();
        $form->form = $request->input('form');
        $form->save();

        return redirect('/Form');
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
        if (Auth::user()->role === 'admin') {
            try {
                $form = Form::find($id);
                $form->delete();

                return redirect('/Form')->with('success', 'Form supprimé');
            } catch (Exception | Throwable $e) {
                throw $e;

                return redirect('/Form')->withErrors('une erreur s\'est produite');
            }
        }
    }
}

<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

use App\Services\UsersService;
use App\User;

class UserController extends Controller
{
    private $usersService;

    public function __construct(UsersService $usersService)
    {
        $this->middleware('auth');
        $this->usersService = $usersService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Response $response
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
            $users = $this->usersService->getUsers(10);

            return view('Users.index')->with('users', $users);
        } else {
            $response->setStatusCode(403);

            return redirect('/');
        }

    }/*
    public function getUsersD()
    {
    	$users = DB::table('users')->select('*');
        return Datatables::of($users)
            ->make(true);
    }*/

    public function settings()
    {
        return view('Users.settings');
    }

    /**
     * @param Request $request
     */
    public function changePassword(Request $request)
    {
        $id = Auth::id();
        $password = $request->input('password');
        $oldPassword = $request->input('old_password');
        try {
            $this->usersService->changePassword($id, $password, $oldPassword);

            return redirect('/settings')->with('success', 'Votre mot de passe a été changé');
        } catch (Exception $e) {
            return redirect('/settings')->with('error', 'Mot de passe incorrect');
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
        $user = User::find($request->get('id'));
        $user->valide = true;
        $user->level = $request->get('level');

        if ($request->get('level')==5||$request->get('level')==7){
            $user->role='Comptoir';
        } 
        if ($request->get('level')==1||$request->get('level')==2||$request->get('level')==3||$request->get('level')==4||$request->get('level')==6){
            $user->role=null;
        } 
        $user->save();

        return redirect('/Users')->with('success', 'L\'inscription de '.$user->name.' a été confirmé');
    }
    public function storeTypeDahat(Request $request)
    {
        $user = User::find($request->get('id'));
        $user->valide = true;
        $user->option = $request->get('option');
        $user->save();

        return redirect('/Users')->with('success', 'L\'inscription de '.$user->name.' a été confirmé');
    }

    /** 
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(int $id, Response $response)
    {
        // $users = $this->usersService->finduser($id);
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
            $user = User::find($id);

            return view('Users.show')->with('user', $user);
        }
        /*return view('Users.show', [
            'users' => $users,
        ]);*/
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
        $user = User::find($id);
		return view('Users.edit')->with('user', $user);
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
            
            $user = User::find($id);
            $user->name = $request->input('name');
            $user->adress = $request->input('adress');
            $user->ville = $request->input('ville');
            $user->email = $request->input('email');
            $user->telephone = $request->input('telephone');
            $user->R_C =$request->input('R_C');
            $user->cod_postal = $request->input('cod_postal');
            $user->I_F = $request->input('I_F');
            $user->A_I = $request->input('A_I');
            $user->credit = $request->input('credit');
            $user->rese = $request->input('rese');

            $user->save();
        }

        return redirect('/Users')->with('succecess', 'user updated');
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
                $user = User::find($id);
                $user->delete();

                return redirect('/Users')->with('success', 'Utilisateur supprimé');
            } catch (Exception | Throwable $e) {
                throw $e;

                return redirect('/Users')->withErrors('une erreur s\'est produite');
            }
        }
    }

    public function fetch(Request $request){
        if ($request->get('query')){
            $query=$request->get('query');
            $users=User::select(array('id','name'))
                ->where('name','like','%'.$query.'%')
                ->get();
            $output='<ul class="dropdown-menu text-center " style="display:block;position: absolute">';
            if (count($users)>0){
                foreach ($users as $user){
                    $output.='<li><a data-id="'.$user->id.'"href="#">'.$user->name.'</a></li>';
                }

            }
            else{
                $output.='<p class="text-center" style="padding: 2px 5px">Aucun Utilisateur à Afficher.</p>';
            }
            $output.='</ul>';
            echo $output;
        }
    }
    public function getRestUser(){
        $user=User::query()
        ->join('total_payments','total_payments.user_id','=','users.id')
        ->addSelect(DB::raw('total_payments.id as idds'))
        ->get();
        return view('inc.navbar')->with('user', $user);
    }

    public function getUserInfo(Request $request)
    {
        $user=User::query()->select("option")->where("id",'=',$request->get('id'))->get();
        return response()->json((["success"=>true,"data"=>$user]));
    }

}

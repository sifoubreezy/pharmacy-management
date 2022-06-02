<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Services\UsersService;
use App\Services\NotificationsService;
use App\TotalPayments;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';
    private $usersService;
    /**
     * @var NotificationsService
     */
    private $notificationsService;

    /**
     * Create a new controller instance.
     *
     * @param UsersService         $usersService
     * @param NotificationsService $notificationsService
     */
    public function __construct(UsersService $usersService, NotificationsService $notificationsService)
    {
        $this->middleware('guest');
        $this->usersService = $usersService;
        $this->notificationsService = $notificationsService;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function register(Request $request)
    {
       // $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }

    protected function validator(array $data)
    {/*
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'adress' => 'required|string|max:255',
            'telephone' => 'required|regex:/(0)[0-9]{9}/',
            'ville' => 'required|string|max:255',
            'cod_postal' => 'required',
            'R_C' => 'required',
            'I_F' => 'required',
            'A_I' => 'required',
        ]);*/
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return \App\Models\User|User
     */
    protected function create(array $data)
    {
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->adress = $data['adress'];
        $user->telephone = $data['telephone'];
        $user->R_C = $data['R_C'];
        $user->ville = $data['ville'];
        $user->cod_postal = $data['cod_postal'];
        $user->I_F = $data['I_F'];
        $user->A_I = $data['A_I'];
        $user->password = bcrypt($data['password']);
        $user->valide = false;
        $user->level = 1; 
        $user->option = 1; 
        $user->credit = 0;
        $user->rese=0;


        if (!$this->usersService->checkIfUsersTableIsEmpty()) {
            $user->role = 'admin';
            $user->valide = true;
            $user->level = 6;
        }
        $this->usersService->createUser($user);

        if ($user) {
            $cart = new Cart();
            $cart->user_id = $user->id;
            $cart->save();
            $totalPayment=new TotalPayments();
            $totalPayment->user_id=$user->id;
            $totalPayment->total_amount=0;
            $totalPayment->rest=0;
            $totalPayment->save();
            if ($user->role !== 'admin') {
                $this->notificationsService->createNotification($user->name.' Attend votre confirmation');
            }
        }

        return redirect('/Users')->with('success', 'Votre demande d\'inscription est en cours de traitement');
    }
    /*
    public function edit($id)
    {
		$user = User::find($id);
		return view('Users.edit')->with('user', $user);
    }
    public function update(Request $request, $id)
    {
		if (Auth::user()->role === 'admin') {
            
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

            $user->save();
        }

        return redirect('/Users')->with('succecess', 'user updated');
    }*/
}

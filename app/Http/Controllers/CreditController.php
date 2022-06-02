<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationsService;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Boncommand;
use App\Fournisseur;
use App\Models\post;
use App\Services\CreditService;

class CreditController extends Controller
{

    private $CreditService;

    public function __construct(CreditService $creditService){
        $this->creditService = $creditService;

    }
    public function credit(){
    $cred = $this->creditService->getCredit();
    if($cred->rest > $cred->credit)
    $this->notificationsService->createNotification(Auth::user()->name.' credit depasse');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     */
    

}

<?php

namespace App\Services;

use App\Repositories\PostRepository;
use App\Repositories\UsersRepository;
use App\Repositories\PurchasesRepository;
use App\Repositories\PurchaseContentRepository;
use App\Repositories\ReturnsRepository;
use App\Repositories\DepositsRepository;
use App\Models\PurchaseContent;
use App\Models\Purchases;
use App\User;
use App\TotalPayments;
use App\Returns;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EcheanceService
{
    //private $postRepository;
    private $purchasesRepository;
    private $usersRepository;
    private $depositsRepository;
    private $returnsRepository;
    public function __construct(PurchasesRepository $purchasesRepository, usersRepository $usersRepository,
    ReturnsRepository $returnsRepository,DepositsRepository $depositsRepository)
    {
        $this->purchasesRepository = $purchasesRepository;
        
        $this->usersRepository = $usersRepository;
        $this->depositsRepository = $depositsRepository;
        $this->returnsRepository = $returnsRepository;

    }

    public function getGains()
    {
       /* return PurchaseContent::query()
            ->join('posts', 'posts.id', '=', 'purchase_content.post_id')
            ->join('purchases', 'purchases.id', '=', 'purchase_content.purchase_id')
            ->join('users', 'users.id', '=', 'purchases.user_id')
            ->select(DB::raw('*'))
            ->addSelect(DB::raw('posts.created_at as creation_date'))
            ->get();
         */    
           /* return Purchases::query()
            ->join('users', 'users.id', '=', 'purchases.user_id')           
            ->join('returns', 'returns.user_id', '=', 'users.id')           
            ->join('deposits', 'deposits.user_id', '=', 'users.id')           
            ->select(DB::raw('*'))
            ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
            ->addSelect(DB::raw('SUM(returns.total) as returns_total'))
            ->addSelect(DB::raw('SUM(deposits.amount) as deposits_total'))
            ->groupBy('purchases.user_id')
            ->addSelect(DB::raw('users.created_at as creation_date'))
            ->get();*/
            return User::query()
            ->select(DB::raw('*'))
            ->get(); 
    }
    public function getSoldee($id)
    {
            return Returns::query()
            ->select(DB::raw('*'))
            ->where("returns.user_id",'=',$id)
            ->get(); 
    }
    public function findEcheanceByUserID($id)
    {
        return User::query()
                       
            ->join('deposits', 'deposits.user_id', '=', 'users.id') 
            ->join('total_payments', 'total_payments.user_id', '=', 'users.id')  
                   
            //->select('users.name','returns.*')
           // ->select('users.name','deposits.*')
           ->select(DB::raw('*'))
            ->where("deposits.user_id",'=',$id)
            //->where("purchases.user_id",'=',$id)
            //->where("returns.user_id",'=',$id)
            //>addSelect(DB::raw('SUM(amount) as final_price'))
            //->groupBy('deposits.user_id')
    
            ->addSelect(DB::raw('users.name as namee'))
            ->addSelect(DB::raw('deposits.created_at as created'))

           ->orderBy('created', 'asc')
            ->distinct('id') 
            ->get()
            //->paginate(10)
            ; 
    }
    public function findEcheancedate($id,$startDate, $endDate)
    {
        return Purchases::query()
            ->join('users', 'users.id', '=', 'purchases.user_id')           
            ->join('returns', 'returns.user_id', '=', 'users.id')           
            ->join('deposits', 'deposits.user_id', '=', 'users.id') 
            ->select('users.name','returns.*')
            ->select('users.name','purchases.*')
            ->select('users.name','deposits.*')
           // ->where("deposits.user_id",'=',$id)
           // ->where("purchases.user_id",'=',$id)
            //->where("returns.user_id",'=',$id)
            ->where('purchases.created_at', '>=', $startDate)
            ->where('purchases.created_at', '<=', $endDate)
        
            ->orderBy('created_at', 'desc')
            ->distinct('id') 
            ->get()
            //->paginate(10)
            ; 
    }
    public function return($id){
        return Returns::query()
        ->join('users', 'users.id', '=', 'returns.user_id')  
        ->join('total_payments', 'total_payments.user_id', '=', 'users.id') 
        ->Leftjoin(DB::raw("(SELECT *,SUM(a.amount) as deposits_total from deposits a GROUP BY a.user_id) d"), 'd.user_id', '=', 'users.id')           
        
        ->select(DB::raw('*'))

        //->select('returns','*')
        //->select('users.name','returns.*')
        ->where("returns.user_id",'=',$id)
        //->addSelect(DB::raw('returns.user_id as cre'))
       // ->addSelect(DB::raw('sum() as ss'))
        //->groupBy('cre')
        ->addSelect(DB::raw('returns.created_at as cre'))

        ->orderBy('cre', 'asc')
        ->distinct('id')
        ->get();
    }
    public function Purchases($id){
        return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id')  
        ->join('total_payments', 'total_payments.user_id', '=', 'users.id') 
        
        ->select(DB::raw('*'))

        //->select('purchases','*')
        //->select('users.name','purchases.*')
        ->addSelect(DB::raw('purchases.created_at as creat'))

        ->where("purchases.user_id",'=',$id)

        ->orderBy('creat', 'asc')
        ->distinct('id')
        ->get();
    }
    public function Purchasesdate($id,$startDate,$endDate){
        return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id')           
        ->select('purchases','*')
        ->select('users.name','purchases.*')
        ->where("purchases.user_id",'=',$id)
        ->where('purchases.created_at', '>=', $startDate)
        ->where('purchases.created_at', '<=', $endDate)
            
        ->orderBy('created_at', 'desc')
        ->distinct('id')
        ->get();
    }
    public function Purchasesfrom($id,$startDate){
        return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id')           
        ->select('purchases','*')
        ->select('users.name','purchases.*')
        ->where("purchases.user_id",'=',$id)
        ->where('purchases.created_at', '>=', $startDate)
        ->orderBy('created_at', 'desc')
        ->distinct('id')
        ->get();
    }
    public function Purchasesto($id,$endDate){
        return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id')           
        ->select('purchases','*')
        ->select('users.name','purchases.*')
        ->where("purchases.user_id",'=',$id)
        ->where('purchases.created_at', '<=', $endDate)  
        ->orderBy('created_at', 'desc')
        ->distinct('id')
        ->get();
    }
/*
    public function findGainsByCreatedAt($startDate, $endDate)
    {
        return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id')           
        ->select(DB::raw('*'))
        ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
        ->where('purchases.created_at', '>=', $startDate)
        ->where('purchases.created_at', '<=', $endDate)
        ->groupBy('purchases.user_id')

        //->select(DB::raw('*'))
        ->addSelect(DB::raw('users.created_at as creation_date'))
        ->get();
    }

    public function findGainsByPostAndCreatedAt($startDate, $endDate, $user)
    {
        return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id')           
        ->select(DB::raw('*'))
        ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
            ->where('users.name', 'LIKE', '%'.$user.'%')
            ->where('purchases.created_at', '>=', $startDate)
            ->where('purchases.created_at', '<=', $endDate)
            ->groupBy('purchases.user_id')   
            //->select(DB::raw('*'))
            ->addSelect(DB::raw('users.created_at as creation_date'))
            ->get();
    }

    public function findGainsByPostAndEndCreatedAt($endDate, $user)
    {
        return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id')           
        ->select(DB::raw('*'))
        ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
            ->where('users.name', 'LIKE', '%'.$user.'%')
            ->where('purchases.created_at', '<=', $endDate)
            ->groupBy('purchases.user_id')
            //->select(DB::raw('*'))
            ->addSelect(DB::raw('users.created_at as creation_date'))
            ->get();
    }

    public function findGainsByEndCreatedAt($endDate)
    {
        return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id')           
        ->select(DB::raw('*'))
        ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
            ->where('purchases.created_at', '<=', $endDate)
            ->groupBy('purchases.user_id')
            //->select(DB::raw('*'))
            ->addSelect(DB::raw('users.created_at as creation_date'))
            ->get();
    }

    public function findGainsByStartCreatedAt($startDate)
    {
        return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id')           
        ->select(DB::raw('*'))
        ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
            ->where('purchases.created_at', '>=', $startDate)
            ->groupBy('purchases.user_id')
            //->select(DB::raw('*'))
            ->addSelect(DB::raw('users.created_at as creation_date'))
            ->get();
    }

    public function findGainsByPostAndStartCreatedAt($startDate, $user)
    {
        return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id')           
        ->select(DB::raw('*'))
        ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
            ->where('users.name', 'LIKE', '%'.$user.'%')
            ->where('purchase_content.created_at', '>=', $startDate)
            ->groupBy('purchases.user_id')
            //->select(DB::raw('*'))
            ->addSelect(DB::raw('users.created_at as creation_date'))
            ->get();
    }

    public function findGainsByPost($user)
    {
        return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id')           
        ->select(DB::raw('*'))
        ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
            ->where('users.name', 'LIKE', '%'.$user.'%')
            ->groupBy('purchases.user_id')
            //->select(DB::raw('*'))
            ->addSelect(DB::raw('users.created_at as creation_date'))
            ->get();
    }

   public function filterResults($debug = [])
    {
        $debug = json_decode(json_encode($debug));

        $debug = array_reduce($debug, function ($acc, $elm) {
            $samePost = array_filter($acc, function ($accElm) use ($elm) {
                return $elm->id === $accElm->id;
            });


            $acc = array_filter($acc, function ($accElm) use ($elm) {
                return $elm->id !== $accElm->id;
            });

            array_push($acc, $elm);

            return $acc;
        }, []);

        return $debug;
    } */
}

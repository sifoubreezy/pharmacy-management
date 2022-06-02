<?php

namespace App\Services;
use App\Repositories\TotalPaymentsRepository;

use App\Repositories\PostRepository;
use App\Repositories\UsersRepository;
use App\Repositories\PurchasesRepository;
use App\Repositories\PurchaseContentRepository;
use App\Repositories\ReturnsRepository;
use App\Repositories\DepositsRepository;
use App\Models\PurchaseContent;
use App\Models\Purchases;
use App\InvoicesRef;
use App\ProviderPayment;
use App\ProviderReturn;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EtatService
{
    //private $postRepository;
    private $totalPaymentsRepository;

    private $purchasesRepository;
    private $usersRepository;
    private $depositsRepository;
    private $returnsRepository;
    public function __construct(PurchasesRepository $purchasesRepository, usersRepository $usersRepository,
    ReturnsRepository $returnsRepository,DepositsRepository $depositsRepository,
    TotalPaymentsRepository $totalPaymentsRepository)
    {
        $this->purchasesRepository = $purchasesRepository;
        $this->totalPaymentsRepository = $totalPaymentsRepository;

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
            return Purchases::query()
            ->join('users', 'users.id', '=', 'purchases.user_id') 
            ->join('total_payments', 'total_payments.user_id', '=', 'users.id')          
            ->Leftjoin(DB::raw("(SELECT *,SUM(a.amount) as deposits_total from deposits a GROUP BY a.user_id) d"), 'd.user_id', '=', 'users.id')           
            ->Leftjoin(DB::raw("(SELECT *,SUM(re.total) as returns_total from returns re GROUP BY re.user_id) r"), 'r.user_id', '=', 'users.id')           
            ->select(DB::raw('*'))
            ->addSelect(DB::raw('users.id as iddd'))

            ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
            ->groupBy('purchases.user_id')
            ->addSelect(DB::raw('users.created_at as creation_date'))
            ->get();
    } 

    public function findGainsByCreatedAt($startDate, $endDate)
    {
 /*       return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id')           
        ->select(DB::raw('*'))
        ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
        ->where('purchases.created_at', '>=', $startDate)
        ->where('purchases.created_at', '<=', $endDate)
        ->groupBy('purchases.user_id')

        //->select(DB::raw('*'))
        ->addSelect(DB::raw('users.created_at as creation_date'))
        ->get();
*/

        return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id') 
        ->join('total_payments', 'total_payments.user_id', '=', 'users.id')          
        ->Leftjoin(DB::raw("(SELECT *,SUM(a.amount) as deposits_total from deposits a GROUP BY a.user_id) d"), 'd.user_id', '=', 'users.id')           
        ->Leftjoin(DB::raw("(SELECT *,SUM(re.total) as returns_total from returns re GROUP BY re.user_id) r"), 'r.user_id', '=', 'users.id')           
        ->select(DB::raw('*'))
        ->where('purchases.created_at', '>=', $startDate)
        ->where('purchases.created_at', '<=', $endDate)
        ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
        ->groupBy('purchases.user_id')
        ->addSelect(DB::raw('users.created_at as creation_date'))
        ->get();
    }

    public function findGainsByPostAndCreatedAt($startDate, $endDate, $user)
    {
       /* return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id')           
        ->select(DB::raw('*'))
        ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
            ->where('users.name', 'LIKE', '%'.$user.'%')
            ->where('purchases.created_at', '>=', $startDate)
            ->where('purchases.created_at', '<=', $endDate)
            ->groupBy('purchases.user_id')   
            //->select(DB::raw('*'))
            ->addSelect(DB::raw('users.created_at as creation_date'))
            ->get();*/

            return Purchases::query()
            ->join('users', 'users.id', '=', 'purchases.user_id')  
            ->join('total_payments', 'total_payments.user_id', '=', 'users.id')         
            ->Leftjoin(DB::raw("(SELECT *,SUM(a.amount) as deposits_total from deposits a GROUP BY a.user_id) d"), 'd.user_id', '=', 'users.id')           
            ->Leftjoin(DB::raw("(SELECT *,SUM(re.total) as returns_total from returns re GROUP BY re.user_id) r"), 'r.user_id', '=', 'users.id')           
            ->select(DB::raw('*'))
            ->where('users.name', 'LIKE', '%'.$user.'%')
            ->where('purchases.created_at', '>=', $startDate)
            ->where('purchases.created_at', '<=', $endDate)
            ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
            ->groupBy('purchases.user_id')
            ->addSelect(DB::raw('users.created_at as creation_date'))
            ->get();
    }

    public function findGainsByPostAndEndCreatedAt($endDate, $user)
    {
        return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id') 
        ->join('total_payments', 'total_payments.user_id', '=', 'users.id')          
        ->Leftjoin(DB::raw("(SELECT *,SUM(a.amount) as deposits_total from deposits a GROUP BY a.user_id) d"), 'd.user_id', '=', 'users.id')           
        ->Leftjoin(DB::raw("(SELECT *,SUM(re.total) as returns_total from returns re GROUP BY re.user_id) r"), 'r.user_id', '=', 'users.id')           
        ->select(DB::raw('*'))
        ->where('users.name', 'LIKE', '%'.$user.'%')
        ->where('purchases.created_at', '<=', $endDate)
        ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
        ->groupBy('purchases.user_id')
        ->addSelect(DB::raw('users.created_at as creation_date'))
        ->get();
    }

    public function findGainsByEndCreatedAt($endDate)
    {
        return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id')  
        ->join('total_payments', 'total_payments.user_id', '=', 'users.id')         
        ->Leftjoin(DB::raw("(SELECT *,SUM(a.amount) as deposits_total from deposits a GROUP BY a.user_id) d"), 'd.user_id', '=', 'users.id')           
        ->Leftjoin(DB::raw("(SELECT *,SUM(re.total) as returns_total from returns re GROUP BY re.user_id) r"), 'r.user_id', '=', 'users.id')           
        ->select(DB::raw('*'))
        ->where('purchases.created_at', '<=', $endDate)
        ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
        ->groupBy('purchases.user_id')
        ->addSelect(DB::raw('users.created_at as creation_date'))
        ->get();
    }

    public function findGainsByStartCreatedAt($startDate)
    {
        return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id')  
        ->join('total_payments', 'total_payments.user_id', '=', 'users.id')         
        ->Leftjoin(DB::raw("(SELECT *,SUM(a.amount) as deposits_total from deposits a GROUP BY a.user_id) d"), 'd.user_id', '=', 'users.id')           
        ->Leftjoin(DB::raw("(SELECT *,SUM(re.total) as returns_total from returns re GROUP BY re.user_id) r"), 'r.user_id', '=', 'users.id')           
        ->select(DB::raw('*'))
        ->where('purchases.created_at', '>=', $startDate)
        ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
        ->groupBy('purchases.user_id')
        ->addSelect(DB::raw('users.created_at as creation_date'))
        ->get();
    }

    public function findGainsByPostAndStartCreatedAt($startDate, $user)
    {
        return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id') 
        ->join('total_payments', 'total_payments.user_id', '=', 'users.id')          
        ->Leftjoin(DB::raw("(SELECT *,SUM(a.amount) as deposits_total from deposits a GROUP BY a.user_id) d"), 'd.user_id', '=', 'users.id')           
        ->Leftjoin(DB::raw("(SELECT *,SUM(re.total) as returns_total from returns re GROUP BY re.user_id) r"), 'r.user_id', '=', 'users.id')           
        ->select(DB::raw('*'))
        ->where('users.name', 'LIKE', '%'.$user.'%')
        ->where('purchases.created_at', '>=', $startDate)
        ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
        ->groupBy('purchases.user_id')
        ->addSelect(DB::raw('users.created_at as creation_date'))
        ->get();
    }

    public function findGainsByPost($user)
    {
        return Purchases::query()
        ->join('users', 'users.id', '=', 'purchases.user_id')  
        ->join('total_payments', 'total_payments.user_id', '=', 'users.id')         
        ->Leftjoin(DB::raw("(SELECT *,SUM(a.amount) as deposits_total from deposits a GROUP BY a.user_id) d"), 'd.user_id', '=', 'users.id')           
        ->Leftjoin(DB::raw("(SELECT *,SUM(re.total) as returns_total from returns re GROUP BY re.user_id) r"), 'r.user_id', '=', 'users.id')           
        ->select(DB::raw('*'))
        ->where('users.name', 'LIKE', '%'.$user.'%')
        ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
        ->groupBy('purchases.user_id')
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
    }
    public function getInvoices($id){
        return InvoicesRef::query()
        ->select(DB::raw('*'))
        ->where('invoices_refs.provider_id','=',$id)
        ->get();
        
    }
    public function getInvoicesFrom($id,$from){
        return InvoicesRef::query()
        ->select(DB::raw('*'))
        ->where('invoices_refs.provider_id','=',$id)
        ->where('invoices_refs.created_date','>=',$from)
        ->get();
    }
    public function getInvoicesTo($id,$to){
        return InvoicesRef::query()
        ->select(DB::raw('*'))
        ->where('invoices_refs.provider_id','=',$id)
        ->where('invoices_refs.created_date','<=',$to)
        ->get();
    }
    public function getInvoicesFromTo($id,$from,$to){
        return InvoicesRef::query()
        ->select(DB::raw('*'))
        ->where('invoices_refs.provider_id','=',$id)
        ->where('invoices_refs.created_date','>=',$from)
        ->where('invoices_refs.created_date','<=',$to)
        ->get();
    }
    public function getProviderPayment($id){
        return ProviderPayment::query()
        ->select(DB::raw('*'))
        ->where('provider_payments.provider_id','=',$id)
        ->get();
        
    }
    public function getProviderPaymentFrom($id,$from){
        return ProviderPayment::query()
        ->select(DB::raw('*'))
        ->where('provider_payments.provider_id','=',$id)
        ->where('provider_payments.created_at','>=',$from)
        ->get();
    }
    public function getProviderPaymentTo($id,$to){
        return ProviderPayment::query()
        ->select(DB::raw('*'))
        ->where('provider_payments.provider_id','=',$id)
        ->where('provider_payments.created_at','<=',$to)
        ->get();
    }
    public function getProviderPaymentFromTo($id,$from,$to){
        return ProviderPayment::query()
        ->select(DB::raw('*'))
        ->where('provider_payments.provider_id','=',$id)
        ->where('provider_payments.created_at','>=',$from)
        ->where('provider_payments.created_at','<=',$to)
        ->get();
    }
    public function getProviderReturn($id){
        return ProviderReturn::query()
        ->select(DB::raw('*'))
        ->where('provider_returns.provider_id','=',$id)
        ->get();
        
    }
    public function getProviderReturnFrom($id,$from){
        return ProviderReturn::query()
        ->select(DB::raw('*'))
        ->where('provider_returns.provider_id','=',$id)
        ->where('provider_returns.created_at','>=',$from)
        ->get();
    }
    public function getProviderReturnTo($id,$to){
        return ProviderReturn::query()
        ->select(DB::raw('*'))
        ->where('provider_returns.provider_id','=',$id)
        ->where('provider_returns.created_at','<=',$to)
        ->get();
    }
    public function getProviderReturnToFrom($id,$from,$to){
        return ProviderReturn::query()
        ->select(DB::raw('*'))
        ->where('provider_returns.provider_id','=',$id)
        ->where('provider_returns.created_at','>=',$from)
        ->where('provider_returns.created_at','<=',$to)
        ->get();
    }
    public function clientPurchase($id){
        return Purchases::query()
        ->select(DB::raw('*'))
        ->where('purchases.user_id','=',$id)
        ->get();
    }
}

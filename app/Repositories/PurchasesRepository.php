<?php

namespace App\Repositories;

use App\Models\PurchaseContent;
use App\Models\Purchases;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 27/06/2018
 * Time: 22:27.
 */
class PurchasesRepository extends CrudRepositoryImpl
{
    /**
     * @param int $userId
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     *
     * @internal param int $user_id
     */
    public function findPurchasesByUserOrderByCreatedAtDesc(int $userId)
    {
        return Purchases::query()
            ->join('purchase_content', 'purchase_content.purchase_id', '=', 'purchases.id')
           // ->join('deposits','deposits.user_id','=','purchases.user_id')
            ->select('purchases.id', 'purchases.user_id', 'purchases.total_price', 'purchases.created_at', 'purchases.payment_method', 'purchase_content.updated_at')//deposits.created_at as lastDeposit
            //->addSelect(DB::raw('sum(deposits.amount) as totalOfDeposits'))
            ->where('purchases.user_id', '=', $userId)
            ->orderBy('purchases.created_at', 'desc')
            ->distinct()
            ->paginate(10);
    }

    public function getAllRecordsByUser(int $userId)
    { 
        return Purchases::query()
        ->join('purchase_content', 'purchase_content.purchase_id', '=', 'purchases.id')
        ->join('posts', 'posts.id', '=', 'purchase_content.post_id')
            ->select('purchases.*', 'purchase_content.*')
            ->where('purchases.user_id', '=', $userId)
            ->where('seen', '=', '0') //seen
            ->where('purchase_content.type', '=', 'post')
            ->orderBy('purchase_content.created_at', 'desc')
            ->groupBy('purchase_content.post_id')
            ->distinct()
            ->paginate(20)
            ;
    }

    public function searchByName($name, $id)
    {
        return PurchaseContent::query()
            ->join('purchases', 'purchase_content.purchase_id', '=', 'purchases.id')
            ->join('posts', 'posts.id', '=', 'purchase_content.post_id')
           ->where('posts.nom_comr', 'like', '%'.$name.'%')
            ->where('user_id', '=', $id)
           ->groupBy('post_id')
            ->get();
    } 

    public function findPurchaseWithPurchaseContents(int $id)
    {
        return Purchases::query()
            ->where('id', '=', $id)
            ->with('purchaseContents')
            ->first();
    }

    public function findPurchaseWithPurchaseContentsByPostId(int $id)
    {
        return PurchaseContent::query()
            ->where('post_id', '=', $id)
            ->orderByDesc('created_at')
            ->first();
    }

    public function findQuantityOfPurchaseWithPurchaseContentsByPostId(int $id)
    {
        return PurchaseContent::query()

            ->where('post_id', '=', $id)
            ->orderByDesc('created_at')
            ->sum('quantity');
    }

    public function getPurchasesByUserOrderByCreatedAt($perpage, $id)
    {
        
        return Purchases::query()
        
            ->where('purchases.user_id', '=', $id)
            ->orderBy('purchases.created_at', 'desc')
            ->groupBy('purchases.user_id')
            ->join('purchase_content', 'purchase_content.purchase_id', '=','purchases.id')
            ->leftJoin('deposits', 'deposits.user_id')
            ->select('purchases.*', 'purchases.payment_method', 'purchases.status')
            ->addSelect(DB::raw('sum(deposits.amount) as totalOfDeposits'))
            ->groupBy('purchase_content.purchase_id')
            ->orderBy('created_at','desc')
            ->paginate($perpage);
    }

    public function getPurchasesByUserAndCreatedAtOrderByNomAsc($perpage, $id, $date, $month = null)
    {
        if ($month == null) {
            return Purchases::query()
                ->where('purchases.user_id', '=', $id)
                ->whereYear('purchases.created_at', '=', $date)
                ->join('purchase_content', 'purchase_content.purchase_id', '=', 'purchases.id')
                ->leftJoin('deposits', 'deposits.user_id')
                ->select('purchases.*', 'purchases.payment_method', 'purchases.status')
                ->addSelect(DB::raw('sum(deposits.amount) as totalOfDeposits'))
                ->groupBy('purchase_content.purchase_id')
                ->orderBy('created_at', 'desc')
                ->paginate($perpage);
        } else {
            return Purchases::query()
                ->where('purchases.user_id', '=', $id)
                ->whereYear('purchases.created_at', '=', $date)
                ->whereMonth('purchases.created_at', '=', $month)
                ->join('purchase_content', 'purchase_content.purchase_id', '=', 'purchases.id')
                ->leftJoin('deposits', 'deposits.user_id')
                ->select('purchases.*', 'purchases.payment_method', 'purchases.status')
                ->addSelect(DB::raw('sum(deposits.amount) as totalOfDeposits'))
                ->groupBy('purchase_content.purchase_id')
                ->orderBy('created_at', 'desc')
                ->paginate($perpage);
        }
    }

    public function getPurchasesOrderByNomAscPaginated(int $perPage)
    {
        return Purchases::query()
            ->join('purchase_content', 'purchase_content.purchase_id', '=', 'purchases.id')
            ->leftJoin('deposits', 'deposits.user_id','=','purchases.user_id')
            ->select('purchases.*', 'purchases.payment_method', 'purchases.status')
            ->addSelect(DB::raw('sum(deposits.amount) as totalOfDeposits'))
            ->addSelect(DB::raw('purchase_content.status as stat'))
            ->groupBy('purchase_content.purchase_id')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getCount()
    {
        return Purchases::all()->count();
    }

    public function getUsersHowPurchased()
    {
        return Purchases::query()
            ->join('users', 'users.id', '=', 'purchases.user_id')
            ->select('users.*')
            ->distinct()
            ->get();
    }

    public function setStatus($purchaseId, $status)
    {
        PurchaseContent::query()->select('*')
            ->where('purchase_id', '=', $purchaseId)
            ->update(['status' => $status]);
            
    }

    public function getTodayPurchases()
    {
        return Purchases::query()
            ->join('users', 'users.id', '=', 'purchases.user_id')
            ->join('purchase_content', 'purchase_content.purchase_id', '=', 'purchases.id')
            ->select('purchases.*', 'purchases.payment_method', 'purchases.status', 'users.name')
            ->whereDate('purchases.updated_at', Carbon::today()->format('Y-m-d'))
            ->addSelect(DB::raw('sum(price) as totalPrice'))
            ->groupBy('purchase_content.purchase_id')
            ->orderBy('purchases.id', 'asc')
            ->get()
            ;
    }

    public function getFilteredDeposits(string $from_date, string $to_date)
    {
        return Purchases::query()
            ->join('users', 'users.id', '=', 'purchases.user_id')
            ->join('purchase_content', 'purchase_content.purchase_id', '=', 'purchases.id')
            ->select('purchases.*', 'purchases.payment_method', 'purchases.status', 'users.name')
            ->whereBetween('purchases.updated_at', array($from_date, $to_date))
            ->addSelect(DB::raw('sum(price) as totalPrice'))
            ->groupBy('purchase_content.purchase_id')
            ->orderBy('purchases.id', 'asc')
            ->get();
    }

    public function getCountTodayPurchases()
    {
        return Purchases::whereDate('purchases.updated_at', Carbon::today()->format('Y-m-d'))->count();
    }
    public function getrestById($id)
    {
        return Purchases::query()
            ->join('users','users.id','=','purchases.user_id')
            ->join('total_payments','total_payments.user_id','=','users.id')
            ->select('total_payments.*','purchases.*','users.*')
            ->where('purchases.id','=',$id)
            ->get();
    }
    public function getusertById($id)
    {
        return Purchases::query()
            ->join('users','users.id','=','purchases.user_id')
            ->select('purchases.*','users.*')
            ->where('purchases.id','=',$id)
            ->get();
    }
    public function findPurchaseWithPurchaseNet($id)
    {
        return Purchases::query()
        ->select('purchases.*')
        ->where('purchases.id','=',$id)
        ->get();
    }
}

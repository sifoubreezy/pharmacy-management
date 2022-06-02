<?php

namespace App\Services;

use App\Repositories\PostRepository;
use App\Repositories\UsersRepository;
use App\Repositories\TotalPaymentsRepository;
use App\Repositories\PurchaseContentRepository;
use App\Repositories\ReturnsRepository;
use App\Repositories\DepositsRepository;
use App\Models\PurchaseContent;
use App\Models\Purchases;
use App\TotalPayments;
use App\User;
use App\Returns;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreditService
{
    //private $postRepository;
    private $TotalPaymentsRepository;
    private $usersRepository;
    private $depositsRepository;
    private $returnsRepository;
    public function __construct(TotalPaymentsRepository $totalPaymentsRepository, usersRepository $usersRepository,
    ReturnsRepository $returnsRepository,DepositsRepository $depositsRepository)
    {
        $this->totalPaymentsRepository = $totalPaymentsRepository;
        
        $this->usersRepository = $usersRepository;
        $this->depositsRepository = $depositsRepository;
        $this->returnsRepository = $returnsRepository;

    }

    public function getCredit()
    {
        return User::query()
        ->join('total_payments', 'total_payments.user_id', '=', 'users.id')           
        ->select(DB::raw('*'))
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

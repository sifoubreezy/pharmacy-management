<?php


namespace App\Services;


use App\Repositories\TotalPaymentsRepository;
use App\TotalPayments;
use Illuminate\Support\Facades\DB;

class TotalPaymentsService
{
    /**
     * @var TotalPaymentsRepository
     */
    private $depositService;

    public function __construct(TotalPaymentsRepository $depositService)
    {
        $this->depositService = $depositService;
    }
    public function getTotal($userId){
        return $this->depositService->getTotal($userId);
    }
    public function getRest($userId){
        return $this->depositService->getRest($userId);
    }
    public function getTotalForAdmin(){
        return $this->depositService->getTotalForAdmin();
    }
    public function totalRest(){
        return TotalPayments::query()
        ->join('users', 'users.id', '=', 'total_payments.user_id')
        ->join('purchases', 'purchases.user_id', '=', 'users.id')

        ->select(DB::raw('*'))
        ->get();
    }
} 
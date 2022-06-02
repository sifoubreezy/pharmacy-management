<?php


namespace App\Repositories;


use App\TotalPayments;

class TotalPaymentsRepository extends CrudRepositoryImpl implements CrudRepository

{
    public function getTotal(int $userId)
    {
        return TotalPayments::query()->where('user_id','=',$userId)->value('total_amount');
    }
    public function getRest(int $userId)
    {
        return TotalPayments::query()->where('user_id','=',$userId)->value('rest');
    }
    public function getTotalForAdmin()
    {
        return TotalPayments::query()->sum('total_amount');
    }
    
}
<?php


namespace App\Repositories;


use App\Deposit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DepositsRepository extends CrudRepositoryImpl implements CrudRepository
{

    public function findDepositsByUserID(int $userId)
    {
        return Deposit::query()->join('users','users.id','=','deposits.user_id')
            ->select('users.name','deposits.*')
            ->where("deposits.user_id",'=',$userId)
            ->orderBy('created_at', 'desc')
            ->distinct('id')
            ->paginate(10)
            ;
    }
    public function findAllDeposits()
    {
        return DB::table('deposits')->join('users','users.id','=','deposits.user_id')
            ->select('users.name','deposits.*',DB::raw('SUM(amount) as sum'))
            ->orderBy('deposits.created_at', 'desc')
            ->groupBy('deposits.user_id')
            ->paginate(10)
            ;
    }

    public function getSum($userId)
    {
        return Deposit::query()->where('user_id','=',$userId)->sum("amount");
    }

    public function getCount()
    {
        return Deposit::all()->count();
    }

    public function getCountByUserId($id)
    {
        return Deposit::query()->where('user_id',"=",$id)->count();
    }

    public function getTodayDeposits()
    {
        return Deposit::query()
            ->join('users','users.id','=','deposits.user_id')
            ->select('users.name','deposits.*')
         ->whereDate("deposits.updated_at",Carbon::today()->format('Y-m-d'))
            ->get();
    }

    public function getFilteredDeposits( $from_date, $to_date)
    {
        return Deposit::query()->join('users','users.id','=','deposits.user_id')
            ->select('users.name','deposits.*')
            ->whereBetween('deposits.updated_at', array($from_date, $to_date))->get();
    }

    public function getCountTodayDeposits()
    {
        return Deposit::whereDate("updated_at",Carbon::today()->format('Y-m-d'))->count();
    }

    public function findAllByUserName($username)
    {

        return DB::table('deposits')->join('users','users.id','=','deposits.user_id')
            ->select('users.name','deposits.*',DB::raw('SUM(amount) as sum'))
            ->where("users.name",'like','%'.$username.'%')
            ->orderBy('deposits.created_at', 'desc')
            ->groupBy('deposits.user_id')
            ->paginate(10);
    }
    public function deleteDepositById($id, $amount, $user_id): void
    {

        DB::table('total_payments')->where('user_id', $user_id)->update([
            'rest' => DB::raw('rest + ' . (float)$amount . '')
        ]);

        DB::table('deposits')->delete($id);
    }
    public function modifyDeposittById($id, float $amount, $user_id): void
    {
        $amountBeforeSubmit = DB::table('deposits')
            ->where('id', $id)->first();

        DB::table('total_payments')->where('user_id', $user_id)->update([
            'rest' => DB::raw('rest + ' . $amountBeforeSubmit->amount . '')
        ]);

        DB::table('total_payments')->where('user_id', $user_id)->update([
            'rest' => DB::raw('rest - ' . $amount . '')
        ]);

        DB::table('deposits')->where('id', $id)->update([
            'amount' => DB::raw($amount)
        ]);
    }
}
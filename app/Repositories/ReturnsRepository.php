<?php


namespace App\Repositories;


use App\ReturnContent;
use App\Returns;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReturnsRepository
{
public function getAllByUserId($id){

    return ReturnContent::

        query()
        ->join("returns",'return_contents.return_id','=','returns.id')
        ->join("posts",'posts.id','=','return_contents.post_id')
        ->where("returns.user_id",'=',$id)
        ->select('posts.qte','posts.pv_ht','return_contents.*','returns.user_id')
        ->addSelect(DB::raw('sum(return_contents.quantity * pv_ht) as total'))
        ->groupBy('return_id')
        ->orderByDesc('returns.created_at')
        ->distinct('returns.id')
        ->paginate(20);
}

    public function getCount()
    {
        return Returns::all()->count();
    }
    public function getCountByUserId($id)
    {
        return Returns::query()->where('user_id','=',$id)->count();
    }

    public function getAllWithTotal()
    {
        return Returns::query()->
        join("return_contents",'return_contents.return_id','=','returns.id')->
        join("posts",'posts.id','=','post_id')->
        join("users",'users.id','=','user_id')->
        select()->addSelect(DB::raw('sum(quantity * pv_ht) as total'))
            ->groupBy('returns.user_id')
            ->orderByDesc('returns.created_at')
        ->get();
    }

    public function getTodayReturns()
    {

        return Returns::query()
        ->join("return_contents",'return_contents.return_id','=','returns.id')
        ->join("posts",'posts.id','=','post_id')
        ->join("users",'users.id','=','user_id')
        ->select('return_contents.*','posts.pv_ht','users.name')
        ->whereDate("return_contents.updated_at",Carbon::today()->format('Y-m-d'))
            ->addSelect(DB::raw('sum(quantity * pv_ht) as total'))
            ->groupBy('returns.id')
        ->get();
    }


    public function getFilteredReturns( $from_date, $to_date)
    {
        return Returns::query()
            ->join("return_contents",'return_contents.return_id','=','returns.id')
            ->join("posts",'posts.id','=','post_id')
            ->join("users",'users.id','=','user_id')
            ->select('return_contents.*','posts.pv_ht',"users.name")
            ->whereBetween('returns.updated_at', array($from_date, $to_date))
            ->addSelect(DB::raw('sum(quantity * pv_ht) as total'))
            ->groupBy('returns.id')
            ->get();
    }

    public function getCountTodayReturns()
    {
        return Returns::whereDate("returns.updated_at",Carbon::today()->format('Y-m-d'))->count();
    }
}
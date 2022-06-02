<?php


namespace App\Repositories;


use App\ProviderReturn;
use App\ProviderReturnContent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProviderReturnRepository
{
    public function getCount()
    {
        return ProviderReturn::all()->count();
    }
    public function getCountByUserId($id)
    {
        return ProviderReturn::query()->where('provider_id','=',$id)->count();
    }

    public function getAllWithTotal()
    {
        return ProviderReturn::query()->
        join("provider_return_contents",'provider_return_contents.return_id','=','provider_returns.id')->
        join("posts",'posts.id','=','post_id')->
        join("fournisseurs",'fournisseurs.id','=','provider_id')->
        select()->addSelect(DB::raw('sum(quantity * prix) as total'))
        ->addSelect(DB::raw('max(provider_returns.id) '))
            ->groupBy('provider_returns.provider_id')
            ->orderByDesc('provider_returns.created_at')
            ->get();
    }

    public function getTodayReturns()
    {

        return ProviderReturn::query()
            ->join("provider_return_contents",'provider_return_contents.return_id','=','provider_returns.id')
            ->join("posts",'posts.id','=','post_id')
            ->join("fournisseurs",'fournisseurs.id','=','provider_id')
            ->select('provider_return_contents.*','posts.prix','fournisseurs.name')
            ->whereDate("return_contents.updated_at",Carbon::today()->format('Y-m-d'))
            ->addSelect(DB::raw('sum(quantity * prix) as total'))
            ->groupBy('provider_returns.id')
            ->get();
    }


    public function getFilteredReturns( $from_date, $to_date)
    {
        return ProviderReturn::query()
            ->join("provider_return_contents",'provider_return_contents.return_id','=','provider_returns.id')
            ->join("posts",'posts.id','=','post_id')
            ->join("fournisseurs",'fournisseurs.id','=','provider_id')
            ->select('provider_return_contents.*','posts.prix',"fournisseurs.name")
            ->whereBetween('provider_returns.updated_at', array($from_date, $to_date))
            ->addSelect(DB::raw('sum(quantity * prix) as total'))
            ->groupBy('provider_returns.id')
            ->get();
    }

    public function getCountTodayReturns()
    {
        return ProviderReturn::whereDate("provider_returns.updated_at",Carbon::today()->format('Y-m-d'))->count();
    }

    public function getAllByUserId($id)
    {
        return ProviderReturnContent::query()
            ->join("provider_returns",'provider_return_contents.return_id','=','provider_returns.id')
            ->join("posts",'posts.id','=','provider_return_contents.post_id')
            ->where("provider_returns.provider_id",'=',$id)
            ->select('posts.qte','posts.prix','provider_return_contents.*','provider_returns.provider_id')
            ->addSelect(DB::raw('sum(provider_return_contents.quantity * prix) as total'))
            ->groupBy('provider_returns.id')
            ->orderByDesc('provider_returns.created_at')
            ->distinct('provider_returns.id')
            ->paginate(20);
    }
}
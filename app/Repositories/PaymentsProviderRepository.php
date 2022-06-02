<?php


namespace App\Repositories;


use App\ProviderPayment;
use Illuminate\Support\Facades\DB;

class PaymentsProviderRepository
{

    public function getAll()
    {
          return DB::table('provider_payments')->join('fournisseurs','fournisseurs.id','=','provider_payments.provider_id')
        ->select('fournisseurs.name','provider_payments.*',DB::raw('SUM(amount) as sum'))
        ->orderBy('provider_payments.created_at', 'desc')
        ->groupBy('provider_payments.provider_id')
        ->paginate(10)
        ;
    }

    public function findAllByUserName($providerName)
    {
        return DB::table('provider_payments')->join('fournisseurs','fournisseurs.id','=','provider_payments.provider_id')
            ->select('fournisseurs.name','provider_payments.*',DB::raw('SUM(amount) as sum'))
            ->where("fournisseurs.name",'like','%'.$providerName.'%')
            ->orderBy('provider_payments.created_at', 'desc')
            ->groupBy('provider_payments.provider_id')
            ->paginate(10);
    }

    public function findAllByUser($id)
    {
        return ProviderPayment::query()->join('fournisseurs','fournisseurs.id','=','provider_payments.provider_id')
            ->select('fournisseurs.name','provider_payments.*')
            ->where("provider_payments.provider_id",'=',$id)
            ->orderBy('created_at', 'desc')
            ->distinct('id')
            ->paginate(10)
            ;
    }

    public function getCount()
    {
        return ProviderPayment::all()->count();
    }
}
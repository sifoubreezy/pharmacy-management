<?php


namespace App\Repositories;
use App\Models\Post;


use App\Fournisseur;
use App\Invoice;
use App\ProviderReturnContent;

class fournisseurRepository
{

    public function getProviderByName($name)
    {
        return Fournisseur::query()->where('name','like','%'.$name.'%')->get();
    }

    public function getAll()
    {
        return Fournisseur::all();
    }

    public function getProvidersHowPurchased()
    {
        return Post::query()
            ->join('invoices_refs','invoices_refs.id','=','posts.ref_invoice_id')
            ->join('fournisseurs','fournisseurs.id','=','invoices_refs.provider_id')
            ->select('fournisseurs.*')
            ->distinct()
            ->get();
    }
    public function findQuantityByPostId(int $id)
    {
        return ProviderReturnContent::query()
            ->where('post_id', '=', $id)
            ->orderByDesc('created_at')
            ->sum("quantity"); //need to change
    }
}
<?php


namespace App\Repositories;
use App\Models\Post;

use Illuminate\Support\Facades\DB;

use App\Invoice;
use Illuminate\Database\Eloquent\Collection;
class InvoiceRepository extends CrudRepositoryImpl implements CrudRepository
{
    public function findee($id){
        return Invoice::find($id);
        
    }
    public function getAllll()
    {
        return Post::query()
            ->join('invoices_refs','invoices_refs.id','=','posts.ref_invoice_id')
            ->join('fournisseurs','fournisseurs.id','=','invoices_refs.provider_id')
            ->leftJoin('categories','categories.id','=','posts.categorie_id')
            ->select('fournisseurs.name','posts.*','categories.categorie','invoices_refs.*')
            ->groupBy('ref_invoice_id')
            ->orderBy('invoices_refs.created_at', 'desc')
            ->get();
    }

    public function getCount()
    {
        return Invoice::all()->count();
    }

    public function getById($id)
    {
        return Post::query()
            ->join('invoices_refs','invoices_refs.id','=','posts.ref_invoice_id')
            ->join('fournisseurs','fournisseurs.id','=','invoices_refs.provider_id')
            ->select('fournisseurs.name','posts.*','invoices_refs.remise','invoices_refs.provider_id','invoices_refs.num_invoice','invoices_refs.total_h_t','invoices_refs.total_net','invoices_refs.created_date')
            ->where('posts.ref_invoice_id','=',$id)
            ->get();
    }

    public function getAllRecordsByProvider(int $provider_id)
    {
        return Invoice::query() 
            ->join('invoices_refs','invoices_refs.id','=','invoices.ref_invoice_id')
            ->where("invoices_refs.provider_id",'=',$provider_id)
            ->orderBy('invoices_refs.created_at', 'desc')
            ->groupBy('post_id')
            ->distinct()
            ->paginate(20)
            ;
    }

}
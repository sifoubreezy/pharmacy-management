<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 27/06/2018
 * Time: 22:27.
 */
class PostRepository extends CrudRepositoryImpl implements CrudRepository
{
    public function getPostsByTagWhereNot(?string $value): Collection
    {
        return Post::all()->where('tag', '!=', $value);
    }

    /**
     * @param int $perPage
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPostsOrderByNomComrAscPaginateds_()
    {
        return Post::query()
        ->where('ref_invoice_id','!=',0)

            ->orderBy('updated_at', 'desc')
            ->get();    
        }
            public function getPostsOrderByNomComrAscPaginated(int $perPage)
            {
                return Post::query()
                ->select(DB::raw('*'))
                ->addSelect(DB::raw('sum(qte) as sumQte'))
                ->where('ref_invoice_id','!=',null)

                ->where('qte','!=',0)
                ->groupBy('posts.pv_ht','posts.date_perm','posts.tag')
                    ->orderBy('posts.id', 'asc')
                    ->paginate($perPage);    }
             public function getPostsOrderByNomComrAscPaginatedInvent(int $perPage)
                {
                return Post::query()
                ->select(DB::raw('*'))
                ->addSelect(DB::raw('sum(qte) as sumQte'))
                ->where('ref_invoice_id','!=',null)

                //->where('qte','!=',0)
                ->groupBy('posts.pv_ht','posts.date_perm')
                    ->orderBy('posts.id', 'asc')
                    ->paginate($perPage);    
                }
                    public function getPostsOrderByNomComrAscPaginatednew(int $perPage)
                    {
                        return Post::query()

                        ->select(DB::raw('*'))
                        ->where('qte','!=',0)
                        ->where('ref_invoice_id','!=',0)

                        ->where('posts.tag','=','Nouveau')
                            ->orderBy('created_at', 'desc')
                            ->paginate($perPage);    }
                            public function getPostsOrderByNomComrAscPaginatedpromo(int $perPage)
                            {
                                return Post::query()

                                ->select(DB::raw('*'))
                                ->where('ref_invoice_id','!=',0)

                                ->where('posts.tag','=','promo')
                                    ->orderBy('posts.created_at', 'asc')
                                    ->paginate($perPage);    }
        
    public function findPostWithForm(int $id)
    {
        return Post::query()->with('form', 'categorie')->where('id', '=', $id)->first();
    }

    public function findPostsByNomComrPaginated(int $perPage, string $nomComr)
    {
        return Post::query()
            ->where('nom_comr', 'like', '%'.$nomComr.'%')
            ->where('ref_invoice_id','!=',0)

            ->where('qte','!=',0)
            ->groupBy('posts.pv_ht','posts.date_perm','posts.qvip','posts.qimport','posts.qplusimport','posts.qord')
            ->orderBy('id', 'asc') 
            ->paginate($perPage);
    }

    public function findPostsByNomComr(string $nomComr)
    {
       
        $parts=explode(' ',$nomComr);
        $test=implode('%',$parts); 
       
       
        $query= Post::query()

        ->select(DB::raw('*'))

        ->addSelect(DB::raw('sum(qte) as sumQte'));
        foreach($parts as $part){
            $query=$query->where('nom_comr', 'like', '%'.$part.'%');
            

        }

            
            
        $query=$query
            ->where('qte','!=',0)
            ->where('ref_invoice_id','!=',0)

            ->groupBy('posts.pv_ht')
            ->orderBy('id', 'desc')
            ->paginate(12);
            return $query;
    }
    public function findPostsByNomComrForInvoice(string $nomComr)
    {
        return Post::query()
            ->where('nom_comr', 'like', '%'.$nomComr.'%')
            //->where('ref_invoice_id','!=',0)

            ->groupBy('prix','date_perm')
            ->orderBy('nom_comr', 'desc')
            ->get();
    }

    public function findAllPostsWhereDatePermLessThen(string $maxdate, $mindate)
    {
        return Post::query()
        ->where('ref_invoice_id','!=',0)

            ->where('posts.date_perm', '<=', $maxdate)
            ->where('posts.date_perm', '>=', $mindate)
            ->paginate(12);
    }

    public function getPostsByCategorie(int $id,int $perPage){
        return Post::query()

            ->where("categorie_id","=",$id)
            ->where('qte','!=',0)
            ->where('ref_invoice_id','!=',0)

            ->groupBy('posts.pv_ht','posts.date_perm','posts.qvip','posts.qimport','posts.qplusimport','posts.qord')
            ->orderBy('id', 'asc')
            ->paginate($perPage);
    }

    public function findForSug($productSugId)
    {
        return Post::query()->with('categorie')->where('id', '=', $productSugId)->first();
    }
}

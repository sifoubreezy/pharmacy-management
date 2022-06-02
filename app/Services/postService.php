<?php

namespace App\Services;
use App\Repositories\InvoicesRefReposetory;

use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 24/06/2018
 * Time: 01:25.
 */

class PostService
{
    private $postRepository;
    private $InvoicesRefReposetory;

    /**
     * PostService constructor.
     *
     * @param $postRepository
     */
    public function __construct(PostRepository $postRepository,InvoicesRefReposetory $invoicesRefReposetory)
    {
        $this->postRepository = $postRepository;
        $this->invoicesRefReposetory = $invoicesRefReposetory;

    }

    public function getPostWithTags() : Collection
    {
        return $this->postRepository->getPostsByTagWhereNot(null);
    }

    public function getPostByTitle($title)
    {
        return $this->postRepository->findPostsByNomComr($title);
    }
    public function findPostsByNomComrForInvoice($title)
    {
        return $this->postRepository->findPostsByNomComrForInvoice($title);
    }

    public function getLastPosts_()
    {
        return $this->postRepository->getPostsOrderByNomComrAscPaginateds_();
    }
    public function getLastPosts(int $perpage)
    {
        return $this->postRepository->getPostsOrderByNomComrAscPaginated($perpage);
    }
    public function getLastPostsInvent(int $perpage)
    {
        return $this->postRepository->getPostsOrderByNomComrAscPaginatedInvent($perpage);
    }
    public function getLastPostsnew($perpage)
    {
        return $this->postRepository->getPostsOrderByNomComrAscPaginatednew($perpage);
    }
    public function getLastPostspromo($perpage)
    {
        return $this->postRepository->getPostsOrderByNomComrAscPaginatedpromo($perpage);
    }
    public function getSearchedPosts(int $perpage, string $nomComr)
    {
        return $this->postRepository->findPostsByNomComrPaginated($perpage, $nomComr);
    }

    public function findPosts(string $nomComr)
    {
        return $this->postRepository->findPostsByNomComr($nomComr);
    }
 
    /**
     * @param array $values
     *
     * @return Post|Model
     */
    public function createPost(array $values)
    {
        $post = new Post();
        foreach ($values as $property => $value) {
            $post->$property = $value;
        }
        $post = $this->postRepository->save($post);

        return $post;
    }
    public function createPostFromInvoice(array $values)
    {
        $post = new Post();
        foreach ($values as $property => $value) {
            $post->$property = $value;
        }
        $post = $this->postRepository->save($post);

        return $post;
    }

    /**
     * @param int $id
     *
     * @return Post|Model
     */
    public function findPost(int $id)
    {
        return $this->postRepository->findPostWithForm($id);
    }

    public function find(int $id)
    {
        return $this->postRepository->find($id);
    }

    public function deletePost(int $id) : void
    {
        $name = $this->find($id)->cover_image;
        if ($name !== 'noimage.jpg') {
            if ($name !== 'noimage.jpg') {
                Storage::delete('images\\' . $name);
            }
        }
        $this->postRepository->delete($id);
    }

    /**
     * @param int   $id
     * @param array $values
     *
     * @return Post|Model
     */
    public function UpdatePost(int $id, array $values)
    {
        $post = $this->findPost($id);
        $name = $post->cover_image;
        foreach ($values as $property => $value) {
            $post->$property = $value;
        }

        if ($post->cover_image === 'noimage.jpg') {
            $post->cover_image = $name;
        } elseif ($post->cover_image !== $name) {
            if ($name !== 'noimage.jpg') {
                Storage::delete('images\\' . $name);
            }
        }
        $post = $this->postRepository->save($post);

        return $post;
    }

    public function date()
    { 
        $date = new \DateTime('+6 months');

        $new = new \DateTime();

        return $this->postRepository->findAllPostsWhereDatePermLessThen($date->format('Y-m-d'), $new->format('Y-m-d'));
    }

    public function searchByCategorie(int $id,int $perPage){
        return $this->postRepository->getPostsByCategorie($id,$perPage);
    }

    public function findForSug($productSugId)
    {
        return $this->postRepository->findForSug($productSugId);
    }
    public function findAllPostsWhereDatePerm($startDate, $endDate)
    {
        return Post::query()
        ->where('date_perm', '>=', $startDate)
        ->where('date_perm', '<=', $endDate)
        
        ->paginate(12);
    } 
    public function findAllPostsWhere( $endDate)
    {
        return Post::query()
        ->where('date_perm', '<=', $endDate)
        
            ->paginate(12);
    } 
    public function findAllPosts($startDate)
    {
        return Post::query()
        ->where('date_perm', '>=', $startDate)
        
            ->paginate(12);
    } 
    public function findAllPostsDDP()
    {
        return Post::query()
        ->select(DB::raw('*'))   
       // ->get()     
            ->paginate(12);
    } 
    public function findPostInovoc()
    {
        return Post::query()
        ->select(DB::raw('*'))   
       ->get();     
       //     ->paginate(12);
    } 
    public function updatePurchase(int $id, array $values)
    {
        $purchaseContent = $this->postRepository->find($id);
       // $post = $this->postService->find($purchaseContent->post_id);
        $totalQte =  $purchaseContent->inventory;
        $prixht= $purchaseContent->prix;
        $prit_vt=$purchaseContent->pv_ht;
        foreach ($values as $property => $value) {
            if ($property === 'quantity') {
                if ($value < $totalQte) { 
                    $purchaseContent->$property = $value;
                    $totalQte = $totalQte - $value;
                } else {
                    $purchaseContent->$property = $totalQte;
                    $totalQte = 0;
                }

            } else {
                $purchaseContent->$property = $value;
            }
        }
        $purchase = $this->invoicesRefReposetory->findee($purchaseContent->ref_invoice_id);
        $purchase->total_h_t = $purchase->total_h_t - $purchaseContent->inventory * $purchaseContent->prix;
        //$purchaseContent->pv_ht =  $purchaseContent->pv_ht;
       /* $this->postRepository->UpdatePost($purchaseContent->id, [
            "qte" => $totalQte,
            "pv_ht"=>$prit_vt,
            "prix"=>$prixht
        ]);*/
        $purchaseContent = $this->postRepository->save($purchaseContent);
        $purchase->total_h_t = $purchase->total_h_t + $purchaseContent->inventory * $purchaseContent->prix;
        $this->invoicesRefReposetory->save($purchase);

        return $purchaseContent;
    }
}

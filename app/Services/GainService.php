<?php

namespace App\Services;
use App\Repositories\TotalPaymentsRepository;
use App\Repositories\PostRepository;
use App\Repositories\ReturnsRepository;
//use App\Repositories\ReturnContentsService;
use App\Repositories\UsersRepository;
use App\Repositories\PurchasesRepository;
use App\Repositories\PurchaseContentRepository;
use App\Models\PurchaseContent;
use App\Models\Purchases;
use Carbon\Carbon;
use App\Returns;
use App\ReturnContent;

use Illuminate\Support\Facades\DB;


class GainService
{
    private $totalPaymentsRepository;
    private $postRepository;
    //private $purchaseContentRepository;
    private $usersRepository;
    private $returnsRepository;
    private $returnContentsService;
    public function __construct(PurchaseContentRepository $purchaseContentRepository, 
    PostRepository $postRepository,
    usersRepository $usersRepository,
    TotalPaymentsRepository $totalPaymentsRepository,
    ReturnsRepository $returnsRepository
    )
    {
        $this->purchaseContentRepository = $purchaseContentRepository;
        $this->postRepository = $postRepository;
        $this->usersRepository = $usersRepository;
        $this->totalPaymentsRepository = $totalPaymentsRepository;
        $this->returnsRepository = $returnsRepository;
        //$this->returnContentsService = $returnContentsService;
    }
    public function getRest(){
        return ReturnContent::query()
        ->join('posts', 'posts.id', '=', 'return_contents.post_id')
        ->join('returns','returns.id','=','return_contents.return_id')
        ->join('users', 'users.id', '=', 'returns.user_id')
        ->select(DB::raw('*'))
        ->addSelect(DB::raw('posts.created_at as creation_date'))
        ->addSelect(DB::raw('return_contents.updated_at as updated_att'))

        ->groupBy('return_contents.id')
        ->orderBy('return_contents.id', 'desc')


       /* ->addSelect(DB::raw('posts.created_at as creation_date'))
        ->addSelect(DB::raw('posts.created_at as creation_date'))
        ->addSelect(DB::raw('posts.created_at as creation_date'))*/
        ->get();
    }

    public function getGains()
    { 
        return PurchaseContent::query()
            ->join('posts', 'posts.id', '=', 'purchase_content.post_id')
            ->join('purchases', 'purchases.id', '=', 'purchase_content.purchase_id')
            ->join('users', 'users.id', '=', 'purchases.user_id')
            ->join('total_payments', 'total_payments.user_id', '=', 'users.id')
            ->select(DB::raw('*'))
            ->addSelect(DB::raw('purchase_content.created_at as creation'))
            ->addSelect(DB::raw('posts.created_at as creation_date'))

            ->addSelect(DB::raw('purchase_content.updated_at as updated_att'))

            ->groupBy('purchase_content.id')
            ->orderBy('purchase_content.id', 'asc')

            ->get();
            
            /*return Purchases::query()
            ->join('users', 'users.id', '=', 'purchases.user_id')           
            ->select(DB::raw('*'))
            ->addSelect(DB::raw('SUM(purchases.total_price) as final_price'))
            ->groupBy('purchases.user_id')
            ->addSelect(DB::raw('posts.created_at as creation_date'))
            ->get();*/
    }

    public function findGainsByCreatedAt($startDate, $endDate)
    {
        return PurchaseContent::query()
            ->join('posts', 'posts.id', '=', 'purchase_content.post_id')
            ->join('purchases', 'purchases.id', '=', 'purchase_content.purchase_id')
            ->join('users', 'users.id', '=', 'purchases.user_id')
            ->where('purchase_content.created_at', '>=', $startDate)
            ->where('purchase_content.created_at', '<=', $endDate)
            ->select(DB::raw('*'))
            ->addSelect(DB::raw('posts.created_at as creation_date'))
            ->get();
    }

    public function findGainsByPostAndCreatedAt($startDate, $endDate, $post)
    {
        return PurchaseContent::query()
            ->join('posts', 'posts.id', '=', 'purchase_content.post_id')
            ->join('purchases', 'purchases.id', '=', 'purchase_content.purchase_id')
            ->join('users', 'users.id', '=', 'purchases.user_id')
            ->where('posts.nom_comr', 'LIKE', '%'.$post.'%')
            ->where('purchase_content.created_at', '>=', $startDate)
            ->where('purchase_content.created_at', '<=', $endDate)
            ->select(DB::raw('*'))
            ->addSelect(DB::raw('posts.created_at as creation_date'))
            ->get();
    }

    public function findGainsByPostAndEndCreatedAt($endDate, $post)
    {
        return PurchaseContent::query()
            ->join('posts', 'posts.id', '=', 'purchase_content.post_id')
            ->join('purchases', 'purchases.id', '=', 'purchase_content.purchase_id')
            ->join('users', 'users.id', '=', 'purchases.user_id')
            ->where('posts.nom_comr', 'LIKE', '%'.$post.'%')
            ->where('purchase_content.created_at', '<=', $endDate)
            ->select(DB::raw('*'))
            ->addSelect(DB::raw('posts.created_at as creation_date'))
            ->get();
    }

    public function findGainsByEndCreatedAt($endDate) 
    {
        return PurchaseContent::query()
            ->join('posts', 'posts.id', '=', 'purchase_content.post_id')
            ->join('purchases', 'purchases.id', '=', 'purchase_content.purchase_id')
            ->join('users', 'users.id', '=', 'purchases.user_id')
            ->whereDate('purchase_content.created_at', '<=', $endDate)//date bug
            ->select(DB::raw('*'))
            ->addSelect(DB::raw('posts.created_at as creation_date'))
            ->get();
    }

    public function findGainsByStartCreatedAt($startDate)
    {
        return PurchaseContent::query()
            ->join('posts', 'posts.id', '=', 'purchase_content.post_id')
            ->join('purchases', 'purchases.id', '=', 'purchase_content.purchase_id')
            ->join('users', 'users.id', '=', 'purchases.user_id')
            ->where('purchase_content.created_at', '>=', $startDate)
            ->select(DB::raw('*'))
            ->addSelect(DB::raw('posts.created_at as creation_date'))
            ->get();
    }

    public function findGainsByPostAndStartCreatedAt($startDate, $post)
    {
        return PurchaseContent::query()
            ->join('posts', 'posts.id', '=', 'purchase_content.post_id')
            ->join('purchases', 'purchases.id', '=', 'purchase_content.purchase_id')
            ->join('users', 'users.id', '=', 'purchases.user_id')
            ->where('posts.nom_comr', 'LIKE', '%'.$post.'%')
            ->where('purchase_content.created_at', '>=', $startDate)
            ->select(DB::raw('*'))
            ->addSelect(DB::raw('posts.created_at as creation_date'))
            ->get();
    }

    public function findGainsByPost($post)
    {
        return PurchaseContent::query()
            ->join('posts', 'posts.id', '=', 'purchase_content.post_id')
            ->join('purchases', 'purchases.id', '=', 'purchase_content.purchase_id')
            ->join('users', 'users.id', '=', 'purchases.user_id')
            ->where('posts.nom_comr', 'LIKE', '%'.$post.'%')
            ->select(DB::raw('*'))
            ->addSelect(DB::raw('posts.created_at as creation_date'))
            ->get();
    }

    public function filterResults($debug = [])
    {
        $debug = json_decode(json_encode($debug));

        $debug = array_reduce($debug, function ($acc, $elm) {
            $samePost = array_filter($acc, function ($accElm) use ($elm) {
                return $elm->id === $accElm->id;
            });

            array_map(function ($e) use ($elm) {
                $elm->quantity = intval($e->quantity) + intval($elm->quantity);
                $elm->price = floatval($e->price) + floatval($elm->price);
            }, $samePost);

            $acc = array_filter($acc, function ($accElm) use ($elm) {
                return $elm->id !== $accElm->id;
            });

            array_push($acc, $elm);

            return $acc;
        }, []);

        return $debug;
    }
}

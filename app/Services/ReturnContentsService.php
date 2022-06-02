<?php


namespace App\Services;


use App\ReturnContent;
use Illuminate\Support\Facades\DB;

class ReturnContentsService
{

    public function getContentById($id)
    {
        return ReturnContent::query()
            ->join('posts','posts.id','=','return_contents.post_id')
            ->select('return_contents.*','posts.nom_comr','posts.qte','posts.pv_ht')
            ->where('return_contents.return_id','=',$id)

            ->get();
    }
    public function changeContentByContentIdAndProductId($returnId,$productId,$quantity): void
    {
        DB::table('return_contents')->where('return_id',$returnId )->where('post_id',$productId)->update([
            'quantity' => DB::raw((int) $quantity)
        ]);
    }
    public function deleteContentByContentIdAndProductId($returnId,$productId): void
    {
        DB::table('return_contents')->where('return_id',$returnId )->where('post_id',$productId)->delete();
    }
}
<?php


namespace App\Services;


use App\ProviderReturnContent;
use Illuminate\Support\Facades\DB;

class ProviderReturnContentService
{
    public function getContentById($id)
    {
        return ProviderReturnContent::query()
            ->join('posts','posts.id','=','provider_return_contents.post_id')
            ->select('provider_return_contents.*','posts.nom_comr','posts.qte','posts.prix')
            ->where('provider_return_contents.return_id','=',$id)

            ->get();
    }
    public function changeContentByContentIdAndProductId($returnId,$productId,$quantity): void
    {
        DB::table('provider_return_contents')->where('return_id',$returnId )->where('post_id',$productId)->update([
            'quantity' => DB::raw((int) $quantity)
        ]);
    }
    public function deleteContentByContentIdAndProductId($returnId,$productId): void
    {
        DB::table('provider_return_contents')->where('return_id',$returnId )->where('post_id',$productId)->delete();
    }
}
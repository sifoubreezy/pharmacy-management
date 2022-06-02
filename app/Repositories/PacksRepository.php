<?php

namespace App\Repositories;

use App\Models\Pack;
use App\Models\pack_post;

/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 26/12/2018
 * Time: 00:13.
 */
class PacksRepository extends CrudRepositoryImpl implements CrudRepository
{

    /**
     * @param int $perpage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPacksWithPackPostWithPostsPaginated(int $perPage)
    {
        return Pack::query()
            ->with('PackPost.post')
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }

    public function findPackPostWithPackId($packId){
        return pack_post::query()
            ->where('post_id','=',$packId)
            ->get();
    }

    public function findPackById(int $id)
    {
        return Pack::query()
            ->find($id);
    }

    public function findPackPostByPackId($id)
    {
        return pack_post::query()
            ->where('pack_id','=',$id)
            ->get();
    }


}

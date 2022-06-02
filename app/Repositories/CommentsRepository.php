<?php
namespace App\Repositories;

use App\Models\Comments;
use Illuminate\Database\Eloquent\Collection;

/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 27/06/2018
 * Time: 22:27
 */

class CommentsRepository extends CrudRepositoryImpl implements CrudRepository
{
    /**
     * @param int $postId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function findAllWithUserByPostId(int $postId)
    {
        return Comments::with('user')
            ->where('post_id', '=', $postId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

}
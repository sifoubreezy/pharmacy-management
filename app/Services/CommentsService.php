<?php
namespace App\Services;

use App\Models\Comments;
use App\Repositories\CommentsRepository;

/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 24/06/2018
 * Time: 01:25
 */

class CommentsService
{
    private $commentsRepository;

    /**
     * PostService constructor.
     * @param CommentsRepository $commentsRepository
     * @internal param $postRepository
     */
    public function __construct(CommentsRepository $commentsRepository)
    {
        $this->commentsRepository = $commentsRepository;
    }

    public function createComment(array $values)
    {
        $comment = new Comments();
        foreach ($values as $property => $value) {
            $comment->$property = $value;
        }
        $comment = $this->commentsRepository->save($comment);
        return $comment;
    }

    public function findAll(int $postId)
    {
        return $this->commentsRepository->findAllWithUserByPostId($postId);
    }

    public function delete(int $id)
    {
        $this->commentsRepository->delete($id);
    }

}
<?php

namespace App\Services;

use App\Models\pack_post;
use App\Repositories\PacksRepository;

/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 25/12/2018
 * Time: 23:51.
 */
class PacksService
{

    private $packsRepository;

    /**
     * PostService constructor.
     *
     * @param PacksRepository $packsRepository
     */
    public function __construct(PacksRepository $packsRepository)
    {

        $this->packsRepository = $packsRepository;
    }

    public function getLastPacks(int $perPage)
    {
        return $this->packsRepository->getPacksWithPackPostWithPostsPaginated($perPage);
    }

    public function findPack($id)
    {
        return $this->packsRepository->find($id);
    }



    public function findPackPostByPackId($id)
    {
        return $this->packsRepository->findPackPostByPackId($id);
    }



    public function findPackById(int $id){
        return $this->packsRepository->findPackById($id);
    }




    public function dropPackPost(int $packPostId)
    {
        pack_post::destroy($packPostId);
    }

}

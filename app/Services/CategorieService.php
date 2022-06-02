<?php

namespace App\Services;

use App\Repositories\CategorieRepository;
use App\Models\Categorie;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 24/06/2018
 * Time: 01:25.
 */
class CategorieService
{
    private $CategorieRepository;

    /**
     * PostService constructor.
     *
     * @param $postRepository
     */
    public function __construct(CategorieRepository $CategorieRepository)
    {
        $this->CategorieRepository = $CategorieRepository;
    }

    public function getCategorie()
    {
        return $this->CategorieRepository->getCategoriesOrderedByCategorieAsc();
    }

    public function getCategoriesPaginated(int $perPage)
    {
        return $this->CategorieRepository->getCategoriesOrderedByCategorieAscPaginated($perPage);
    }
}

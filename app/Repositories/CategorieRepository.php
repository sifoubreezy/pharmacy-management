<?php

namespace App\Repositories;

use App\Models\Categorie;
use Illuminate\Database\Eloquent\Collection;
/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 27/06/2018
 * Time: 22:27.
 */
class CategorieRepository extends CrudRepositoryImpl implements CrudRepository
{
    public function getCategoriesOrderedByCategorieAsc()
    {
        return Categorie::orderBy('categorie', 'asc')->get();
    }

    public function getCategoriesOrderedByCategorieAscPaginated(int $perPage)
    {
        return Categorie::query()
            ->orderBy('categorie', 'asc')
            ->paginate($perPage);
    }
}

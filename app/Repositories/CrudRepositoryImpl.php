<?php
/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 27/06/2018
 * Time: 22:32
 */

namespace App\Repositories;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CrudRepositoryImpl implements CrudRepository
{

    /**
     * @param Model $model
     * @return Model
     */
    public function save(Model $model): Model
    {
        $model->saveOrFail();
        return $model;
    }

    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        $className = str_replace('Repository', '', get_class($this));
        $className = str_replace('Repositories', 'Models', $className);
        $className::destroy($id);
    }

    /**
     * @param array $ids
     */
    public function deleteAll(array $ids): void
    {
        $className = str_replace('Repository', '', get_class($this));
        $className = str_replace('Repositories', 'Models', $className);
        $className::destroy($ids);
    }


    /**
     * @param int $id
     * @return Model
     */
    public function find(int $id): Model
    {
        $className = str_replace('Repository', '', get_class($this));
        $className = str_replace('Repositories', 'Models', $className);
        return $className::find($id);
    }

    /**
     * @return object
     */
    public function getAll(): Collection
    {
        $className = str_replace('Repository', '', get_class($this));
        $className = str_replace('Repositories', 'Models', $className);
        return $className::all();

    }
}
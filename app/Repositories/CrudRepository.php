<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 27/06/2018
 * Time: 22:25
 */
interface CrudRepository
{
    public function save(Model $model): Model;

    public function delete(int $id): void;

    public function deleteAll(array $ids): void;

    public function getAll(): Collection;

    public function find(int $id): Model;
}
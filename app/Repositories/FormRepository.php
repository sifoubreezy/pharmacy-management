<?php

namespace App\Repositories;

use App\Models\Form;

/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 27/06/2018
 * Time: 22:27.
 */
class FormRepository extends CrudRepositoryImpl implements CrudRepository
{
    public function getFormsOrderedByFormAsc()
    {
        return Form::orderBy('form', 'asc')->paginate(10);
    }

    public function getFormsOrderedByFormAscPaginated(int $perPage)
    {
        return Form::query()
            ->orderBy('form', 'asc')
            ->paginate($perPage);
    }

}

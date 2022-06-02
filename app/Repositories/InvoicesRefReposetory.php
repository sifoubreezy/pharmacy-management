<?php

namespace App\Repositories;

/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 19/07/2018
 * Time: 03:08
 */
use App\InvoicesRef;
class InvoicesRefReposetory extends CrudRepositoryImpl implements CrudRepository
{
    public function findee($id){
        return InvoicesRef::find($id);
        
    }

}
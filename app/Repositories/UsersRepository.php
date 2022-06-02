<?php

namespace App\Repositories;

use App\User;

/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 27/06/2018
 * Time: 22:24.
 */
class UsersRepository extends CrudRepositoryImpl implements CrudRepository
{
    /**
     * @return User
     */
    public function findFirstUser(): ?User
    {
        return User::all()->first();
    }

    public function getAllUsersOrderedByNameAsc(int $perPage)
    {
        return User::query()
            ->leftJoin('total_payments','users.id','=','total_payments.user_id')
            ->select('users.*','total_payments.total_amount','total_payments.rest')
            ->orderBy('name', 'asc')->
            paginate($perPage);
    }

    public function findPostWithForm(int $id)
    {
        return User::query()->with('name')->where('id', '=', $id)->first();
    }
}

<?php
/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 30/06/2018
 * Time: 16:36.
 */

namespace App\Services;

use App\Repositories\UsersRepository;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersService
{
    private $usersRepository;

    /**
     * UsersService constructor.
     *
     * @param $usersRepository
     */
    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function checkIfUsersTableIsEmpty(): bool
    {
        return $this->usersRepository->findFirstUser() !== null;
    }

    public function createUser(Model $user): void
    {
        $this->usersRepository->save($user);
    }

    public function getUsers(int $perPage)
    {
        return $this->usersRepository->getAllUsersOrderedByNameAsc($perPage);
    }

    public function getUserInfo($id)
    {
        return $this->usersRepository->find($id);
    }

    /**
     * @param int    $id
     * @param string $password
     * @param string $oldPassword
     *
     * @throws Exception
     */
    public function changePassword(int $id, string $password, string $oldPassword)
    {
        try {
            DB::beginTransaction();
            $this->checkPasswordIsCorrect($id, $oldPassword);
            $user = User::find($id);
            $user->password = bcrypt($password);
            $user->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception('Mot de passe Incorrect');
        }
    }

    /**
     * @param int    $id
     * @param string $password
     *
     * @throws Exception
     */
    public function checkPasswordIsCorrect(int $id, string $password): void
    {
        $user = User::find($id);
        if (!Hash::check($password, $user->password)) {
            throw new Exception('incorrect password !');
        }
    }

    public function finduser(int $id)
    {
        return $this->UsersRepository->findPostWithForm($id);
    }
}

<?php


namespace App\Repositories;


use App\Models\Notifications;

class NotificationsRepository extends CrudRepositoryImpl implements CrudRepository
{

    public function getNotificationsOrderByCreatedAtDescPaginated(int $perPage)
    {
        return Notifications::query()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getNotificationsByCategorieOrderByCreatedAtDescPaginated(int $perPage,string $categorie)
    {
        return Notifications::query()
            ->where('categorie','=',$categorie)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findCountNotificationsByQueryWhereNotSeen(string $categorie){
        return Notifications::query()
            ->where('seen', '=',false)
            ->where('categorie','=',$categorie)
            ->count();
    }

    public function findCountNotificationsWhereNotSeen(){
        return Notifications::query()
            ->where('seen', '=',false)
            ->count();
    }

}

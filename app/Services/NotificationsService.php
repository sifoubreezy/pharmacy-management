<?php

namespace App\Services;

use App\Models\Notifications;
use App\Repositories\NotificationsRepository;

class NotificationsService
{

    protected $notificationsRepository;

    function __construct(NotificationsRepository $notificationsRepository)
    {
        $this->notificationsRepository = $notificationsRepository;
    }

    public function createNotification(string $content,string $type=null)
    {
        $notification = new Notifications();
        $notification->content = $content;
        if(!!$type){
            $notification->categorie = $type;
        }
        $notification = $this->notificationsRepository->save($notification);

        return $notification;
    }

    public function getLastNotificationPaginated(int $perPage)
    {
        return $this->notificationsRepository->getNotificationsOrderByCreatedAtDescPaginated($perPage);
    }

    public function getLastNotificationByQueryPaginated(int $perPage, string $query)
    {
        return $this->notificationsRepository->getNotificationsByCategorieOrderByCreatedAtDescPaginated($perPage, $query);
    }

    public function markNotificationAsSeenOrUnseen(int $id, bool $seen)
    {
        $notification = $this->notificationsRepository->find($id);
        $notification->seen = $seen;
        $this->notificationsRepository->save($notification);
    }

    public function getCountUnseenNotificationByQuery(string $query): int
    {
        return $this->notificationsRepository->findCountNotificationsByQueryWhereNotSeen($query);
    }

    public function getCountUnseenNotification(): int
    {
        return $this->notificationsRepository->findCountNotificationsWhereNotSeen();
    }

}

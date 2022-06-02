<?php

namespace App\Http\Controllers;

use App\Services\NotificationsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class notificationsController extends Controller
{
    /**
     * @var NotificationsService
     */
    private $notificationsService;

    public function __construct(NotificationsService $notificationsService)
    {
        $this->middleware('auth');
        $this->notificationsService = $notificationsService;
    }

    public function index(Request $request)
    {
        $query = $request->query('categorie');
        if (!$query) {
            $notifications = $this->notificationsService->getLastNotificationPaginated(15);
            $count = $this->notificationsService->getCountUnseenNotification();
        } else {
            $notifications = $this->notificationsService->getLastNotificationByQueryPaginated(15, $query);
            $count = $this->notificationsService->getCountUnseenNotificationByQuery($query);
        }
        return view('notifications.index')->with('notifications', $notifications)->with('count', $count)->with('query',$query);
    }

    public function update(Request $request, Response $response)
    {
        try {
            $seenValue = boolval($request->get('seen'));
            $id = $request->get('id');
            $this->notificationsService->markNotificationAsSeenOrUnseen($id, $seenValue);
            $request->session()->put('count', $this->notificationsService->getCountUnseenNotification());
        } catch (Exception | Throwable $e) {
            //throw $e;
            $response->setStatusCode(400, 'Invalid Inputs');
        }
        return redirect('/notifications');
    }

}

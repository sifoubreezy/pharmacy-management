<?php

namespace App\Http\Controllers;

use App\Services\NotificationsService;
use App\Services\PostService;
use App\Services\PacksService;
use App\Slider;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    private $postService;
    private $packsService;
    /**
     * @var NotificationsService
     */
    private $notificationsService;

    public function __construct(PostService $postService, NotificationsService $notificationsService, PacksService $packsService)
    {
        $this->postService = $postService;
        $this->packsService = $packsService;
        $this->notificationsService = $notificationsService;
    }

    public function index(Request $request)
    {
        $request->session()->put('count',$this->notificationsService->getCountUnseenNotification());
        $posts = $this->postService->getPostWithTags();
        $packs = $this->packsService->getLastPacks(12);
        $slides=Slider::all();
        return view('pages.index', ['posts' => $posts,'packs' => $packs,'slides' => $slides]);

    }

    public function search(Request $request, $title)
    {
        return ['title' => $this->postService->getPostByTitle($title)];

    }

    public function about(Request $request)
    {
        $about = file_get_contents(public_path().'/docs/about.txt');
        return view('pages.about',compact('about'));
    }

}

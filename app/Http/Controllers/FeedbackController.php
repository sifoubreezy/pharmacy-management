<?php

namespace App\Http\Controllers;
use App\Services\FeedbackService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FeedbackController extends Controller

{
    private $feedbackService;

    public function __construct(FeedbackService $feedbackService)
    {
        $this->feedbackService=$feedbackService;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|Application|RedirectResponse|View
     */
    public function create()
    {
        if (Auth::user()->role !== 'admin') {
        return View('feedback.create');
    }else{
            return redirect()->back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $title=$request->title;
        $content=$request->feedbackContent;

        $this->validate($request,[

                'title'=>"required",
                'feedbackContent'=>'required'
        ]);
        $this->feedbackService->createFeedback($title,$content);
        return redirect()->back();

    }

}

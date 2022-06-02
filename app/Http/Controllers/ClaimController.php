<?php

namespace App\Http\Controllers;

use App\Services\DepositServices;
use App\Services\FeedbackService;
use App\Services\ReturnsService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClaimController extends Controller
{
    private $feedbackService;
    private $returnsService;
    private $depositService;

    public function __construct(DepositServices $depositService,FeedbackService $feedbackService,ReturnsService $returnsService)
{
    $this->feedbackService=$feedbackService;
    $this->returnsService=$returnsService;
    $this->depositService=$depositService;
}

    /**
     * Display a listing of the resource.
     *
     * @return Factory|Application|RedirectResponse|View
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
        $countFeedback=$this->feedbackService->getCountFeedbackByUserId(Auth::id());
        $countReturn=$this->returnsService->getCountByUserId(Auth::id());
        $countDeposit=$this->depositService->getCountByUserId(Auth::id());

        return View('claim/index',['countFeedback'=>$countFeedback,'countReturn'=>$countReturn,'countDeposit'=>$countDeposit]);
    }else{
            return redirect()->back();
        }
    }


}

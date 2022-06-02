<?php


namespace App\Repositories;


use App\Feedback;
use Illuminate\Support\Facades\Auth;

class FeedbackRepository
{

    public function createFeedback($title,$content){
        $feedback=new feedback;
        $feedback->user_id=Auth::id();
        $feedback->title=$title;
        $feedback->content=$content;
        $feedback->slug=str_slug($title);
        $feedback->save();
    }
    public function getFeedback(){
        return Feedback::query()->join('users','users.id','=','feedback.user_id')
            ->select('users.name','feedback.*')
            ->orderBy('created_at', 'desc')
            ->groupBy('user_id')
            ->paginate(10);
    }
    public function getFeedbackByUserId($id){
        return Feedback::select('id','feedback.title','feedback.created_at')
            ->where('user_id','=',$id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }
    public function getFeedbackById($id)
    {
        return Feedback::select('*')
            ->where('id','=',$id)
            ->first();

    }
    public function getCount()
    {
    return Feedback::all()->count();
    }
    public function getCountBYUserId($id)
    {
        return Feedback::all()->where('user_id','=',$id)->count();
    }
}
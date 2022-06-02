<?php


namespace App\Services;


use App\Repositories\FeedbackRepository;

class FeedbackService
{
    private $feedbackRepository;
    public function __construct(FeedbackRepository $feedbackRepository){
        $this->feedbackRepository=$feedbackRepository;
}
    public function createFeedback($title,$content){

$this->feedbackRepository->createFeedback($title,$content);
}
    public function getAllFeedback()
    {
        return $this->feedbackRepository->getFeedback();
    }


    public function getFeedbackByUserId($id)
    {
        return $this->feedbackRepository->getFeedbackByUserId($id);
    }

    public function getFeedbackById($id)
    {
        return$this->feedbackRepository->getFeedbackById($id);
    }

    public function getCountFeedback()
    {
        return$this->feedbackRepository->getCount();
    }
    public function getCountFeedbackByUserId($id)
    {
        return$this->feedbackRepository->getCountBYUserId($id);
    }
}
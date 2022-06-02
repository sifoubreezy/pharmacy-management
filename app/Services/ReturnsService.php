<?php


namespace App\Services;


use App\Repositories\ReturnsRepository;
use App\Returns;

class ReturnsService
{
    /**
 * @var ReturnsRepository
 */private $returnsRepository;
    public function __construct(ReturnsRepository $returnsRepository)
    {
        $this->returnsRepository=$returnsRepository;
    }

    public function getAllByUserId($id)
    {
    return $this->returnsRepository->getAllByUserId($id);
    }

    public function getCount()
    {
        return $this->returnsRepository->getCount();
    }
    public function getCountByUserId($id)
    {
        return $this->returnsRepository->getCountByUserId($id);
    }

    public function getAllWithTotal()
    {
        return $this->returnsRepository->getAllWithTotal();
    }

    /**
     * @return ReturnsRepository
     */
    public function create($id,$total) 
    {
        $return=new Returns();
        $return->user_id=$id;
        $return->total=$total;
        $return->save();
        return $return;
    }

    public function getTodayReturns()
    {
        return $this->returnsRepository->getTodayReturns();
    }



    public function getFilteredReturns( $from_date, $to_date)
    {
        return $this->returnsRepository->getFilteredReturns($from_date,$to_date);
    }

    public function getCountTodayReturns()
    {
        return $this->returnsRepository->getCountTodayReturns();
    }
}
<?php


namespace App\Services;


use App\ProviderReturn;
use App\ProviderReturnContent;
use App\Repositories\ProviderReturnRepository;
use Illuminate\Support\Facades\DB;

class ProviderReturnService
{


    /**
     * @var ProviderReturnRepository
     */private $providerReturnRepository;
    public function __construct(ProviderReturnRepository $providerReturnRepository)
    {
        $this->providerReturnRepository=$providerReturnRepository;
    }

    public function getAllByUserId($id){

       return $this->providerReturnRepository->getAllByUserId($id);
    }

    public function getCount()
    {
        return $this->providerReturnRepository->getCount();
    }
    public function getCountByUserId($id)
    {
        return $this->providerReturnRepository->getCountByUserId($id);
    }

    public function getAllWithTotal()
    {
        return $this->providerReturnRepository->getAllWithTotal();
    }

    /**
     * @return ProviderReturn
     */
    public function create($id,$total)
    {
        $return=new ProviderReturn();
        $return->provider_id=$id;
        $return->total=$total;
        $return->save();
        return $return;
    }

    public function getTodayReturns()
    {
        return $this->providerReturnRepository->getTodayReturns();
    }



    public function getFilteredReturns( $from_date, $to_date)
    {
        return $this->providerReturnRepository->getFilteredReturns($from_date,$to_date);
    }

    public function getCountTodayReturns()
    {
        return $this->providerReturnRepository->getCountTodayReturns();
    }

}
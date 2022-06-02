<?php


namespace App\Services;


use App\Repositories\DepositsRepository;

class DepositServices
{
    /**
     * @var DepositsRepository
     */
    private $depositRepository;

    public function __construct(DepositsRepository $depositRepository)
    {
        $this->depositRepository = $depositRepository;
    }
    public function findAllByUser(int $userId)
    {
        return $this->depositRepository->findDepositsByUserID($userId);
    }
    public function findAllDeposits()
    {
        return $this->depositRepository->findAllDeposits();
    }

    public function getCount()
    {
        return $this->depositRepository->getCount();
    }

    public function getSum($userId)
    {
        return $this->depositRepository->getSum($userId);
    }
    public function getCountByUserId($id)
    {
        return $this->depositRepository->getCountByUserId($id);
    }

    public function getTodayDeposits()
    {
        return $this->depositRepository->getTodayDeposits();
    }


    public function getFilteredDeposits( $from_date,  $to_date)
    {
        return $this->depositRepository->getFilteredDeposits($from_date,$to_date);
    }

    public function getCountTodayDeposits()
    {
        return $this->depositRepository->getCountTodayDeposits();
    }

    public function findAllByUserName($username)
    {
        return $this->depositRepository->findAllByUserName($username);
    }

    public function deleteDepositById($id,$amount,$user_id): void
    {
         $this->depositRepository->deleteDepositById($id,$amount,$user_id);
    }
 
    public function modifyDepositById($id, float $amount, int $user_id): void
    {
         $this->depositRepository->modifyDeposittById($id,$amount,$user_id);
    }
}
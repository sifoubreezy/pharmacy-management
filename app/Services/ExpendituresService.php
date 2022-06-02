<?php


namespace App\Services;


use App\Repositories\ExpendituresRepository;

class ExpendituresService
{

    private $expendituresRepository;
public function __construct(ExpendituresRepository $expendituresRepository)
{
    $this->expendituresRepository=$expendituresRepository;
}

    public function getAll()
    {
            return $this->expendituresRepository->getAll();
    }

    public function getTotal()
    {
        return $this->expendituresRepository->getTotal();
    }

    public function getById(int $id)
    {
        return $this->expendituresRepository->getById($id);
    }

    public function getCount()
    {
        return $this->expendituresRepository->getCount();
    }

    public function getTodayExpenditures()
    {
        return $this->expendituresRepository->getTodayExpenditures();
    }

    public function getFilteredExpenditures($from_date, $to_date)
    {
        return $this->expendituresRepository->getFilteredExpenditures($from_date, $to_date);
    }

    public function getCountTodayExpenditures()
    {
        return $this->expendituresRepository->getCountTodayExpenditures();
    }

}
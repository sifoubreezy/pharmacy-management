<?php


namespace App\Services;


use App\Repositories\PaymentsProviderRepository;

class PaymentsProviderService
{

    /**
     * @var PaymentsProviderRepository
     */
    private $paymentsProviderRepository;

    public function __construct(PaymentsProviderRepository $paymentsProviderRepository)
{
    $this->paymentsProviderRepository=$paymentsProviderRepository;

}

    public function getAll()
    {
        return $this->paymentsProviderRepository->getAll();
    }

    public function findAllByUserName($providerName)
    {
        return $this->paymentsProviderRepository-> findAllByUserName($providerName);
    }

    public function findAllByUser($id)
    {
        return $this->paymentsProviderRepository->findAllByUser($id);
    }

    public function getCount()
    {
        return $this->paymentsProviderRepository->getCount();
    }
}
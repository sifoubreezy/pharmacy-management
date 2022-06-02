<?php


namespace App\Services;


use App\Repositories\TotalPaymentProviderRepository;

class TotalPaymentProviderService
{

    /**
     * @var TotalPaymentProviderRepository
     */
    private $totalPaymentProviderRepository;

    public function __construct(TotalPaymentProviderRepository $totalPaymentProviderRepository)
    {
    $this->totalPaymentProviderRepository=$totalPaymentProviderRepository;
    }

    public function getRest($id)
    {
        return $this->totalPaymentProviderRepository->getRest($id);
    }

    public function deletePaymentById($id,$amount,$provider_id): void
    {
         $this->totalPaymentProviderRepository->deletePaymentById($id,$amount,$provider_id);
    }

    public function modifyPaymentById($id, float $amount, int $provider_id): void
    {
         $this->totalPaymentProviderRepository->modifyPaymentById($id,$amount,$provider_id);
    }
}
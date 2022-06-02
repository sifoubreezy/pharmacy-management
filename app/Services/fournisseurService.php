<?php


namespace App\Services;


use App\Repositories\fournisseurRepository;

class fournisseurService
{
    /**
     * @var fournisseurRepository
     */
    private $fournisseurRepository;
    public function __construct(fournisseurRepository $fournisseurRepository)
    {
        $this->fournisseurRepository=$fournisseurRepository;
    }
    public function getProviderByName($name)
    {
        return $this->fournisseurRepository->getProviderByName($name);
    }

    public function getAll()
    {
        return $this->fournisseurRepository->getAll();
    }

    public function getProvidersHowPurchased()
    {
        return $this->fournisseurRepository->getProvidersHowPurchased();
    }
    public function findQuantityByPostId(int $id)
    {
        return $this->fournisseurRepository->findQuantityByPostId($id);
    }

}
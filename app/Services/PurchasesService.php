<?php

namespace App\Services;
use App\Models\PurchaseContent;

use App\Models\Purchases;
use App\Repositories\PurchasesRepository;
use Illuminate\Support\Facades\DB;

/**
 * Created by IntelliJ IDEA.
 * User: KamarMEDDAH
 * Date: 24/06/2018
 * Time: 01:25.
 */
class PurchasesService
{
    /**
     * @var PurchasesRepository
     */
    private $purchasesRepository;

    /**
     * PurchasesService constructor.
     *
     * @param PurchasesRepository $purchasesRepository
     */
    public function __construct(PurchasesRepository $purchasesRepository)
    {
        $this->purchasesRepository = $purchasesRepository;
    }

    public function createPurchase(array $values)
    {
        $purchase = new Purchases();
        foreach ($values as $property => $value) {
            $purchase->$property = $value;
        }
        $purchase = $this->purchasesRepository->save($purchase);

        return $purchase;
    }

    public function findAllByUser(int $userId)
    {
        return $this->purchasesRepository->findPurchasesByUserOrderByCreatedAtDesc($userId);
    }

    public function getAllRecordsByUser(int $userId)
    {
        return $this->purchasesRepository->getAllRecordsByUser($userId);
    }

    public function searchByName($name, $id)
    {
        return $this->purchasesRepository->searchByName($name, $id);
    }
    public function getUsersHowPurchased(){
        return $this->purchasesRepository->getUsersHowPurchased();
    }

    public function setStatus($purchaseId,$status)
    {
        $this->purchasesRepository->setStatus($purchaseId,$status);
    }
 
    public function find(int $id)
    {
        return $this->purchasesRepository->findPurchaseWithPurchaseContents($id);
    }
    public function findNet(int $id)
    {
        return $this->purchasesRepository->findPurchaseWithPurchaseNet($id);
    }

    public function findByPostId(int $id)
    {
        return $this->purchasesRepository->findPurchaseWithPurchaseContentsByPostId($id);
    }

    public function findQuantityByPostId(int $id)
    {
        return $this->purchasesRepository->findQuantityOfPurchaseWithPurchaseContentsByPostId($id);
    }

    public function getPurchasesByUserOrderByNomAsc($perpage, $id)
    {
        return $this->purchasesRepository->getPurchasesByUserOrderByCreatedAt($perpage, $id);
    }

    public function getPurchasesByUserAndCreatedAtOrderByNomAsc($perpage, $id, $date, $month = null)
    {
        if ($month == null) {
            return $this->purchasesRepository->getPurchasesByUserAndCreatedAtOrderByNomAsc($perpage, $id, $date);
        } else {
            return $this->purchasesRepository->getPurchasesByUserAndCreatedAtOrderByNomAsc($perpage, $id, $date, $month);
        }
    }

    public function getPurchasesOrderByNomAsc(int $perpage)
    {
        return $this->purchasesRepository->getPurchasesOrderByNomAscPaginated($perpage);
    }

    public function UpdatePurchase(int $id, array $values)
    {
        $purchase = $this->purchasesRepository->find($id);
        foreach ($values as $property => $value) {
            $purchase->$property = $value; 
        }
        $purchase = $this->purchasesRepository->save($purchase);

        return $purchase;
    }
    public function UpdatePurchaseNet(int $id, array $values)
    {
        $purchase = $this->purchasesRepository->find($id);
        foreach ($values as $property => $value) {
            $purchase->$property = $value; 
        }
        $purchase = $this->purchasesRepository->save($purchase);

        return $purchase;
    }
    public function getCount()
    {
        return $this->purchasesRepository->getCount();
    }

//    public function getUsersHowPurchased()
//    {
//        return $this->purchasesRepository->getUsersHowPurchased();
//    }
    public function getTodayPurchases()
    {
        return $this->purchasesRepository->getTodayPurchases();
    }

    public function getFiltredPurchases()
    {
        return $this->purchasesRepository->getFilteredDeposits();

    }

    public function getFilteredPurchases( $from_date, $to_date)
    {
        return $this->purchasesRepository->getFilteredDeposits( $from_date,  $to_date);
    }

    public function getCountTodayPurchases()
    {
        return $this->purchasesRepository->getCountTodayPurchases();
    }
    public function getrestPurchases($id)
    {
        return $this->purchasesRepository->getrestById($id);
    }
    public function getuserPurchases($id)
    {
        return $this->purchasesRepository->getusertById($id);
    }
    public function modifyDeposittById($id, float $total_price, $user_id): void
    {
        $amountBeforeSubmit = DB::table('purchases')
            ->where('id', $id)->first();
        $gg=Purchases::query()
        ->select('*')
        ->where('id','=', $id)
        ->get();
        /*DB::table('total_payments')->where('user_id', $user_id)->update([
            'rest' => DB::raw('rest + ' . $total_price . '')
        ]);
*/
$bb=PurchaseContent::query()
->select('*')
->where('purchase_id','=',$id)
->get();
        DB::table('total_payments')->where('user_id', $user_id)->update([
            'rest' => DB::raw('rest - ' . $total_price . '')
        ]);
        
       // PurchaseContent::find($id)->delete();
       foreach($bb as $mm){
        DB::table('posts')
        ->where('id','=', $mm->post_id)
        ->update([
            'qte' => DB::raw( $mm->quantity )
        ]);    }
        foreach($gg as $amo){
            DB::table('purchase_content')
            ->where('purchase_id', $amo->id)->delete();
        }
        Purchases::find($id)->delete();
        
    }
}

<?php


namespace App\Services;
use Illuminate\Support\Facades\DB;

use App\InvoicesRef;
use App\Repositories\InvoiceRepository;
use App\Repositories\InvoicesRefReposetory;

class InvoicesService
{
    /**
     * @var InvoiceRepository
     */
    private $postService;
    private $InvoiceRepository;
    private $InvoicesRefReposetory;
    public function __construct(InvoiceRepository $invoiceRepository,PostService $postService,InvoicesRefReposetory $invoicesRefReposetory
    )
    {
        $this->InvoiceRepository=$invoiceRepository;
        $this->postService = $postService;
        $this->invoicesRefReposetory = $invoicesRefReposetory;

    }

    public function getAll()
    {
        return $this->InvoiceRepository->getAllll();
    }
    public function getCount(): int
    {
        return $this->InvoiceRepository->getCount();
    }

    public function getById($id)
    {
        return $this->InvoiceRepository->getById($id);
    }

    public function getAllRecordsByProvider(int $user_id)

    {
        return $this->InvoiceRepository->getAllRecordsByProvider($user_id);
    }/*
    public function UpdatePurchase(int $id, array $values)
    {
        $purchase = $this->purchasesRepository->find($id);
        foreach ($values as $property => $value) {
            $purchase->$property = $value; 
        }
        $purchase = $this->InvoiceRepository->save($purchase);

        return $purchase;
    }*/
    public function updatePurchase(int $id, array $values)
    {
        $purchaseContent = $this->InvoiceRepository->findee($id);
        $post = $this->postService->find($purchaseContent->post_id);
        $totalQte = $post->qte + $purchaseContent->quantity;
        $prixht= $purchaseContent->ppa;
        $prit_vt=$purchaseContent->pv_ht;
        foreach ($values as $property => $value) {
            if ($property === 'quantity') {
                if ($value < $totalQte) { 
                    $purchaseContent->$property = $value;
                    $totalQte = $totalQte - $value;
                } else {
                    $purchaseContent->$property = $totalQte;
                    $totalQte = 0;
                }

            } else {
                $purchaseContent->$property = $value;
            }
        }
        $purchase = $this->invoicesRefReposetory->findee($purchaseContent->ref_invoice_id);
        $purchase->total_h_t = $purchase->total_h_t - $purchaseContent->quantity * $purchaseContent->ppa;
       // $purchaseContent->ppa = $purchaseContent->quantity * $purchaseContent->ppa;
        $this->postService->UpdatePost($post->id, [
            "qte" => $totalQte,
            "pv_ht"=>$prit_vt,
            "prix"=>$prixht
        ]);
        $purchaseContent = $this->InvoiceRepository->save($purchaseContent);
        $purchase->total_h_t = $purchase->total_h_t + $purchaseContent->quantity * $purchaseContent->ppa;
        $this->invoicesRefReposetory->save($purchase);

        return $purchaseContent;
    }
    public function modifyDeposittById($id, float $total_price, $user_id): void
    {
        $amountBeforeSubmit = DB::table('invoices_refs')
            ->where('id', $id)->first();
            $gg=InvoicesRef::query()
            ->select('*')
            ->where('id','=', $id)
            ->get();
        /*DB::table('total_payments')->where('user_id', $user_id)->update([
            'rest' => DB::raw('rest + ' . $total_price . '')
        ]);
*/
        DB::table('total_payment_providers')->where('provider_id', $user_id)->update([
            'rest' => DB::raw('rest - ' . $total_price . '')
        ]);
        foreach($gg as $amo){
           
            DB::table('posts')
            ->where('ref_invoice_id', $amo->id)->delete();
        }
        
        InvoicesRef::find($id)->delete(); 
        
    }
}
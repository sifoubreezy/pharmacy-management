<?php

namespace App\Http\Controllers;

use App\Services\DepositServices;
use App\Services\ExpendituresService;
use App\Services\PurchasesService;
use App\Services\ReturnsService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DailyReportController extends Controller
{

    /**
     * @var DepositServices
     */
    private $depositService;
    /**
     * @var PurchasesService
     */
    private $purchasesService;

    /**
     * @var ReturnsService
     */
    private $returnService;
    private $expendituresService;

    public function __construct(PurchasesService $purchasesService,ExpendituresService $expendituresService,DepositServices $depositService ,ReturnsService $returnService)
    {
        $this->middleware('auth');
        $this->purchasesService=$purchasesService;
        $this->returnService=$returnService;
        $this->depositService=$depositService;
        $this->expendituresService=$expendituresService;
    }


    public function index()
   {

            $purchases=$this->purchasesService->getTodayPurchases();
            $returns=$this->returnService->getTodayReturns();
            $deposits=$this->depositService->getTodayDeposits();

           return View('journal.Treasury.DailyReport',["deposits"=>$deposits,'returns'=>$returns,"purchases"=>$purchases]);
    }

    public function refresh()
    {
        $purchases=$this->purchasesService->getTodayPurchases();
        $returns=$this->returnService->getTodayReturns();
        $deposits=$this->depositService->getTodayDeposits();
        $output='';
        foreach ($deposits as $deposit){
            $output.='<tr>
                            
                            <td>'.$deposit->name.'</td>
                                <td><a href="../Treasury/DepositsOfUser/'.$deposit->user_id.'" target="_blank">voir plus</a></td>
                            <td  style="display: none">'.$deposit->updated_at.'</td>
                            <td>'. Carbon::parse($deposit->updated_at)->format('Y-m-d').' ('.Carbon::parse($deposit->updated_at)->diffForHumans().')'.'</td>
                            <td><span class="text-success">Versement</span></td>
                            <td>'.$deposit->amount.' DA</td>
                        </tr>';
        }
        foreach ($returns as $return){
            $output.='<tr>
                          
                            <td>'.$return->name.'</td>
                            <td><a href="../Return_content/'.$return->return_id.'" target="_blank">voir plus</a></td>
                            <td  style="display: none">'.$return->updated_at.'</td>
                              <td>'. Carbon::parse($return->updated_at)->format('Y-m-d').' ('.Carbon::parse($return->updated_at)->diffForHumans().')'.'</td>
                            <td><span class="text-danger">Bon De Retour</span></td>
                            <td>'.$return->total.' DA</td>
                        </tr>';
        }
        foreach ($purchases as $purchase){
            $output.='<tr>
                           
                            <td>'.$purchase->name.'</td>
                          <td><a href="../Treasury/purchase_content/'.$purchase->purchase_id.'" target="_blank">voir plus</a></td>
                            <td  style="display: none">'.$purchase->updated_at.'</td>
                             <td>'. Carbon::parse($purchase->updated_at)->format('Y-m-d').' ('.Carbon::parse($purchase->updated_at)->diffForHumans().')'.'</td>
                            <td><span class="text-info">Bon De Commande</span></td>
                            <td>'.$purchase->totalPrice.' DA</td>
                        </tr>';
        }
        echo $output;

    }
    public function fetchData(Request $request)
    {
        $from_date= $request->get('from_date');
        $to_date= $request->get('to_date');
        $purchases=$this->purchasesService->getFilteredPurchases($from_date,$to_date);
        $returns=$this->returnService->getFilteredReturns($from_date,$to_date);
        $deposits=$this->depositService->getFilteredDeposits($from_date,$to_date);
        $output='';
        foreach ($deposits as $deposit){
            $output.='<tr>
                            
                            <td>'.$deposit->name.'</td>
                             <td><a href="../Treasury/DepositsOfUser/'.$deposit->user_id.'" target="_blank">voir plus</a></td>
                            <td  style="display: none">'.$deposit->updated_at.'</td>
                             <td>'. Carbon::parse($deposit->updated_at)->format('Y-m-d').' ('.Carbon::parse($deposit->updated_at)->diffForHumans().')'.'</td>
                            <td><span class="text-success">Versement</span></td>
                            <td>'.$deposit->amount.' DA</td>
                        </tr>';
        }
        foreach ($returns as $return){
            $output.='<tr>
                          
                            <td>'.$return->name.'</td>
                            <td><a href="DepositsOfUser/'.$return->id.'">voir plus</a></td>
                            <td  style="display: none">'.$return->updated_at.'</td>
                            <td>'. Carbon::parse($return->updated_at)->format('Y-m-d').' ('.Carbon::parse($return->updated_at)->diffForHumans().')'.'</td>
                            <td><span class="text-danger">Bon De Retour</span></td>
                            <td>'.$return->total.' DA</td>
                        </tr>';
        }
        foreach ($purchases as $purchase){
            $output.='<tr>
                           
                            <td>'.$purchase->name.'</td>
                            <td><a href="../Treasury/purchase_content/'.$purchase->id.'" target="_blank">voir plus</a></td>
                            <td  style="display: none">'.$purchase->updated_at.'</td>
                              <td>'. Carbon::parse($purchase->updated_at)->format('Y-m-d').' ('.Carbon::parse($purchase->updated_at)->diffForHumans().')'.'</td>
                            <td><span class="text-info" style="">Bon De Commande</span></td>
                            <td>'.$purchase->totalPrice.' DA</td>
                        </tr>';
        }
        echo $output;
    }

}

<?php

namespace App\Http\Controllers;

use App\Expenditure;
use App\Services\DepositServices;
use App\Services\ExpendituresService;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExpenditureController extends Controller
{
    /**
     * @var ExpendituresService
     */
    private $expendituresService ;
    private $depositService;

    public function __construct(ExpendituresService $expendituresService,DepositServices $depositService)
    {
        $this->expendituresService=$expendituresService;
        $this->depositService=$depositService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Factory|Application|View
     */
    public function indexForDailyReport(){
        $expenditures=$this->expendituresService->getTodayExpenditures();
        $deposits=$this->depositService->getTodayDeposits();
        return View('journal.Treasury.ExpendituresDailyReport',["expenditures"=>$expenditures,'deposits'=>$deposits]);

    }
    public function index()
    {
        $Expenditures=$this->expendituresService->getAll();
        $total=$this->expendituresService->getTotal();
        return view('journal/Treasury/expenditures_journal',["Expenditures" => $Expenditures,'Total'=>$total]);

    }
    public function create()
    {
        return view('journal/Treasury/createExpenditure');

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {


        DB::table('capital')->update([
            'amount' => DB::raw('amount - ' . (float)$request->get('amount') . '')
        ]);

        $expenditure=new Expenditure();
        $expenditure->title=$request->get('title');
        $expenditure->description=$request->get('description');
        $expenditure->amount=$request->get('amount');
        $expenditure->type=$request->get('type');
        $expenditure->save();
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Factory|Application|View
     */
    public function show($id)
    {
        $Expenditure=$this->expendituresService->getById($id);
        return  View('journal/Treasury/ShowExpenditure',['Expenditure'=>$Expenditure]);
    }
    public function fetchData(Request $request)
    {
        $from_date= $request->get('from_date');
        $to_date= $request->get('to_date');
        $output='';
        $expenditures=$this->expendituresService->getFilteredExpenditures($from_date,$to_date);
        $deposits=$this->depositService->getFilteredDeposits($from_date,$to_date);

        foreach ($expenditures as $expenditure){
            $output.='<tr>
                            
                            <td>'.$expenditure->title.'</td>
                             <td><a href="../Treasury/DepositsOfUser/'.$expenditure->id.'" target="_blank">voir plus</a></td>
                            <td  style="display: none">'.$expenditure->updated_at.'</td>
                             <td>'. Carbon::parse($expenditure->updated_at)->format('Y-m-d').' ('.Carbon::parse($expenditure->updated_at)->diffForHumans().')'.'</td>
                            <td><span class="text-success">depance</span></td>
                            <td>'.$expenditure->amount.' DA</td>
                        </tr>';
        }
        foreach ($deposits as $deposit){
            $output.='<tr>
                            
                            <td>'.$deposit->name.'</td>
                             <td><a href="../Treasury/DepositsOfUser/'.$deposit->user_id.'" target="_blank">voir plus</a></td>
                            <td  style="display: none">'.$deposit->updated_at.'</td>
                             <td>'. Carbon::parse($deposit->updated_at)->format('Y-m-d').' ('.Carbon::parse($deposit->updated_at)->diffForHumans().')'.'</td>
                            <td><span class="text-info">Versement</span></td>
                            <td>'.$deposit->amount.' DA</td>
                        </tr>';
        }

        echo $output;
    }
    public function refresh()
    {

        $expenditure=$this->expendituresService->getTodayExpenditures();
        $deposits=$this->depositService->getTodayDeposits();
        $output='';

        foreach ($expenditure as $expenditure){
            $output.='<tr>
                            
                            <td>'.$expenditure->title.'</td>
                             <td><a href="../Treasury/DepositsOfUser/'.$expenditure->id.'" target="_blank">voir plus</a></td>
                            <td  style="display: none">'.$expenditure->updated_at.'</td>
                             <td>'. Carbon::parse($expenditure->updated_at)->format('Y-m-d').' ('.Carbon::parse($expenditure->updated_at)->diffForHumans().')'.'</td>
                            <td><span class="text-success">depance</span></td>
                            <td>'.$expenditure->amount.' DA</td>
                        </tr>';
        }
        foreach ($deposits as $deposit){
            $output.='<tr>
                            
                            <td>'.$deposit->name.'</td>
                             <td><a href="../Treasury/DepositsOfUser/'.$deposit->user_id.'" target="_blank">voir plus</a></td>
                            <td  style="display: none">'.$deposit->updated_at.'</td>
                             <td>'. Carbon::parse($deposit->updated_at)->format('Y-m-d').' ('.Carbon::parse($deposit->updated_at)->diffForHumans().')'.'</td>
                            <td><span class="text-info">Versement</span></td>
                            <td>'.$deposit->amount.' DA</td>
                        </tr>';
        }

        echo $output;

    }

}

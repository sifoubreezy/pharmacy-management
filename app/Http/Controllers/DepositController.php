<?php

namespace App\Http\Controllers;

use App\Deposit;
use App\Models\PurchaseContent;
use App\Services\DepositServices;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DepositController extends Controller
{
    private $depositService;
    public function __construct(DepositServices $depositService)
    {
        $this->depositService=$depositService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|Application|RedirectResponse|View
     */
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
        $allDeposits=$this->depositService->findAllByUser(Auth::id());
        return view('claim/Deposit/index',['deposits'=>$allDeposits]);

    }else{
            return redirect()->back();
        }
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Exception
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

        $amount= (float)$request->get('amount');
        $purchasesId=$request->get('purchases');
        $user=$request->get('user_id');
        $deposit=new Deposit();
        $deposit->user_id=$user;
        $deposit->amount=$amount;


        if ($deposit->save()) {

            DB::table('total_payments')->where('user_id',$user )->update([
                'rest' => DB::raw('rest - ' . $amount . '')
            ]);


            for ($i=count($purchasesId)-1;$i>=0;$i--)
            {

                $query=PurchaseContent::query()
                    ->select()
                    ->where('payment_method', '=', '3')
                    ->where('purchase_content.purchase_id', '=', $purchasesId[$i])
                    ->addSelect(DB::raw('sum(price) as total'))
                    ->orderBy('purchase_content.created_at','asc')
                    ->groupBy('purchase_id');


                $purchases =$query->get();

               if (!empty($purchases))
               {

                   foreach ($purchases as $purchase)
                   {
                       $price= (float)$purchase->price;

                       $updated_price= (float)$purchase->updated_price;

                       if ($price!==$updated_price){

                           if ($amount>=0){

                               $sum=$updated_price+$amount;

                               if ($price >=$sum)
                               {
                                   $query->update(['updated_price'=>DB::raw((float)$sum)]);
                                   $amount=$amount-$sum;

                               }
                               else{

                                   $query->update(['updated_price'=>DB::raw((float)$price)]);
                                   $amount=abs($amount-$sum);

                               }
                           }
                       }
                   }
               }
            }
        }
     DB::commit();

    }catch (\Exception $e)
        {
            DB::rollBack();
            return $e;
        }
    }

    public function applyDeposit(Request $request)
    {


            $amount= (float)$request->get('amount');
            $no = $request->get('no');
            $user=$request->get('user_id');
            $saving = $request->input('saving');
            $deposit=new Deposit();
            $deposit->user_id=$user;
            $deposit->amount=$amount;
            $deposit->no=$no; 
            $deposit->saving = intval($saving);


            if ($deposit->save()) {

                DB::table('total_payments')->where('user_id',$user )->update([
                    'rest' => DB::raw('rest - ' . $amount . '')
                ]);
    }


    }

    public function SearchDeposit(Request $request)
    {
        $username=$request->get('username');
        $rows=$this->depositService->findAllByUserName($username);
        $output='';
        $i=1;
        if (count($rows)>0){

            foreach ($rows as $row){
            $output.='  <tr class="table-row">
                                <td class="column-1">'.$i.'</td>
                                <td class="column-2"><a
                                            href="DepositsOfUser/'.$row->user_id.'">'.$row->name.'</a></td>
                                <td class="column-3">'.$row->sum.' DA </td>
                            </tr>';
            $i++;
        }

        }else{
            $output='Nothing to show';
    }
        echo $output;
    }
    public function destroy(Deposit $Deposit)
    {
        //
    }
    public function deleteDepositById(Request $request)
    {
        $id=$request->get('id');
        $amount= (float)$request->get('amount');
        $user_id= $request->get('user_id');
        $this->depositService->deleteDepositById($id,$amount,$user_id);
    }

    public function modifyDepositById(Request $request)
    {
        $id=$request->get('id');
        $amount= (float)$request->get('amount'); 
        $user_id= $request->get('user_id');
        $this->depositService->modifyDepositById($id,$amount,$user_id);

    }
}



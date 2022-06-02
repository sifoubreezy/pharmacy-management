<?php

namespace App\Http\Controllers;

use App\Boncommand;
use App\Fournisseur;
use App\Models\Post;
use App\Models\PurchaseContent;
use App\Models\Purchases;
use App\ProviderReturnContent;
use App\Services\CartContentService;
use App\Services\CartService;
use App\Services\DepositServices;
use App\Services\ExpendituresService;
use App\Services\FeedbackService;
use App\Services\InvoicesService;
use App\Services\NotificationsService;
use App\Services\PacksService;
use App\Services\PaymentsProviderService;
use App\Services\PostService;
use App\Services\PurchaseContentService;
use App\Services\PurchasesService;
use App\Services\ReturnContentsService;
use App\Services\ReturnsService;
use App\Services\TotalPaymentsService;
use App\TotalPayments;
use App\User;
use Carbon\Carbon;
use Exception;
use HttpException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JournalController extends Controller
{
    /**
     * @var PurchasesService
     */
    private $purchasesService;
    /**
     * @var DepositServices
     */
    private $depositService;

    /**
     * @var PostService
     */
    private $postService;
    /**
     * @var PurchaseContentService
     */
    private $purchaseContentService;
    /**
     * @var CartService
     */
    private $cartService;
    /**
     * @var CartContentService
     */
    /**
     * @var TotalPaymentsService
     */
    private $totalPaymentsService;

    private $cartContentService;
    /**
     * @var NotificationsService
     */
    private $notificationsService;
    /**
     * @var PacksService
     */
    private $packsService;
    /**
     * @var FeedbackService
     */
    private $feedbackService;
    /**
     * @var ReturnsService
     */
    private $returnsService;
    /**
     * @var ReturnContentsService
     */
    private $returnContentsService;

    /**
     * @var ExpendituresService
     */
    private $expendituresService;
    /**
     * @var InvoicesService
     */
    private $invoicesService;
    /**
     * @var PaymentsProviderService
     */
    private $paymentsProviderService;

    /**
     * PurchasesController extends constructor.
     * @param ExpendituresService $expendituresService
     * @param ReturnContentsService $returnContentsService
     * @param FeedbackService $feedbackService
     * @param ReturnsService $returnsService
     * @param TotalPaymentsService $totalPaymentsService
     * @param DepositServices $depositService
     * @param PurchasesService $purchasesService
     * @param PostService $postService
     * @param PurchaseContentService $purchaseContentService
     * @param CartService $cartService
     * @param CartContentService $cartContentService
     * @param NotificationsService $notificationsService
     * @param InvoicesService $invoicesService
     */
    public function __construct(PaymentsProviderService $paymentsProviderService, InvoicesService $invoicesService, ExpendituresService $expendituresService, ReturnContentsService $returnContentsService, FeedbackService $feedbackService, ReturnsService $returnsService, TotalPaymentsService $totalPaymentsService, DepositServices $depositService, PurchasesService $purchasesService, PostService $postService, PurchaseContentService $purchaseContentService, CartService $cartService, CartContentService $cartContentService, NotificationsService $notificationsService)
    {
       // $this->middleware('auth');
        $this->purchasesService = $purchasesService;
        $this->purchaseContentService = $purchaseContentService;
        $this->depositService = $depositService;
        $this->totalPaymentsService = $totalPaymentsService;
        $this->feedbackService = $feedbackService;
        $this->returnsService = $returnsService;
        $this->returnContentsService = $returnContentsService;
        $this->expendituresService = $expendituresService;
        $this->postService = $postService;
        $this->invoicesService = $invoicesService;
        $this->paymentsProviderService = $paymentsProviderService;
        $this->cartContentService = $cartContentService;
        $this->cartService = $cartService;


    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|Application|View
     */
    public function index()
    {
        if (Auth::user()->role === "admin"||Auth::user()->level == 5|| Auth::user()->level == 7) {
            $countFeedback = $this->feedbackService->getCountFeedback();
            $countDeposit = $this->depositService->getCount();
            $countPurchase = $this->purchasesService->getCount();
            $countReturn = $this->returnsService->getCount();
            $countExpenditureDailyReport = $this->depositService->getCountTodayDeposits() + $this->expendituresService->getCountTodayExpenditures();
            $countDailyReport = $this->depositService->getCountTodayDeposits() + $this->purchasesService->getCountTodayPurchases() + $this->returnsService->getCountTodayReturns() + $this->expendituresService->getCountTodayExpenditures();
            $countUsers = User::all()->count();
            $countfournisuers = Fournisseur::all()->count();

            $countboncommands = Boncommand::all()->count();
            $countPayments = $this->paymentsProviderService->getCount();
            $countInvoices = $this->invoicesService->getCount();
            $countProviderReturn = ProviderReturnContent::all()->count();

            return View('journal.index',
                ["countFeedback" => $countFeedback, "countDeposit" => $countDeposit, 'countProviderReturn' => $countProviderReturn,
                    "countPurchase" => $countPurchase, "countPayments" => $countPayments, "countDailyReport" => $countDailyReport, 'countExpenditureDailyReport' => $countExpenditureDailyReport, 'countUser' => $countUsers, 'countReturn' => $countReturn, 'countInvoices' => $countInvoices, 'countfournisuers' => $countfournisuers, 'countboncommands' => $countboncommands]);

        } else {
            return redirect()->back();
        }
    }

    public function indexOfDeposit()
    {
        $users = User::all()->where('role', '!=', 'admin');

        $allDeposits = $this->depositService->findAllDeposits();
        return view('journal/Treasury/deposit_journal', ['deposits' => $allDeposits,'users'=>$users]);
    }

    public function indexOfTreasury()
    {
        $countDeposit = $this->depositService->getCount();
        $countPurchase = $this->purchasesService->getCount();
        $countDailyReport = $this->depositService->getCountTodayDeposits() + $this->purchasesService->getCountTodayPurchases() + $this->returnsService->getCountTodayReturns();
        $countExpenditure = $this->expendituresService->getCount();
        $countExpenditureDailyReport = $this->depositService->getCountTodayDeposits() + $this->expendituresService->getCountTodayExpenditures();
        $countPayments = $this->paymentsProviderService->getCount();

        return View('journal.Treasury.index',
            ["countDeposit" => $countDeposit, "countPayments" => $countPayments, "countPurchase" => $countPurchase, 'countExpenditureDailyReport' => $countExpenditureDailyReport, 'countDailyReport' => $countDailyReport, 'countExpenditure' => $countExpenditure]);

    }

    public function setStatus(Request $request): void
    {

        $purchaseId = $request->get('purchaseId');
        $status = $request->get('status'); 
        $this->purchasesService->setStatus($purchaseId, $status);

    }

    public function indexOfFeedback()
    {
        if (Auth::user()->role === 'admin'||Auth::user()->level == 5) {

            $feedback = $this->feedbackService->getAllFeedback();
            return view("journal/Feedback_journal")->with("feedback", $feedback);
        } else {
            return redirect()->back();
        }

    }

    public function indexOfReturn(Request $request)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->level == 5|| Auth::user()->level == 7) {

            $returns = $this->returnsService->getAllWithTotal();
            return view("journal/Return_journal")->with("returns", $returns);
        } else {
            return redirect()->back();
        }
    }

    public function indexOfPayment(Request $request)
    {
        if (Auth::user()->role === 'admin'|| Auth::user()->level == 7 || Auth::user()->level == 5) {
            $name = $request->query('user', null);
            $year = $request->query('year', null);
            $month = $request->query('month', null);
            $total = 0;

            $users = User::all()->where('role', '!=', 'admin')
                ->where('role', '!=', 'Comptoir');
            if (Auth::user()->role === 'admin'|| Auth::user()->level == 7 || Auth::user()->level == 5) {
                $total = $this->totalPaymentsService->getTotalForAdmin();

                if ($name == null) {
                    $purchases = $this->purchasesService->getPurchasesOrderByNomAsc(10);

                } else {
                    $user = User::query()->where('name', 'like', '%' . $name . '%')->first();
                    if ($user != null) {
                        if ($year != null) {
                            if ($month != null) {
                                $purchases = $this->purchasesService->getPurchasesByUserAndCreatedAtOrderByNomAsc(10, $user->id, $year, $month);
                            } else {
                                $purchases = $this->purchasesService->getPurchasesByUserAndCreatedAtOrderByNomAsc(10, $user->id, $year);
                            }
                        } else {
                            $purchases = $this->purchasesService->getPurchasesByUserOrderByNomAsc(10, $user->id);
                        }
                    } else {
                        return redirect('/purchases')->withErrors('not found');
                    }
                }
                return view('journal/Treasury/purchases_journal', ['purchases' => $purchases, "users" => $users, 'name' => $name, 'month' => $month, 'year' => $year, 'total' => $total]);
            }

        } else {
            return redirect()->back();
        }

    }

    public function updatePurchaseContents(int $id, Request $request)
    {
        if (Auth::user()->role === "admin"|| Auth::user()->level == 7) {
            $quantity = $request->input('quantity');
            $unit_price = $request->input('unit_price');
            $itemremise = $request->input('itemremise');
            $values = [
                "quantity" => $quantity,
                "unit_price" => $unit_price,
                "itemremise" => $itemremise
            ];
            return $this->purchaseContentService->updatePurchase($id, $values);
        } else {
            throw new HttpException('Forbidden', 403);
        }
    }
 
    public function createPurchaseContents(Request $request)
    {
        if (Auth::user()->role === "admin"|| Auth::user()->level == 7) {
            try {
                DB::beginTransaction();
                $quantity = $request->input('quantity');
                $unit_price = $request->input('unit_price');
                $post_id = $request->input('post_id');
                $purchase_id = $request->input('purchase_id');
                $values = [
                    "quantity" => $quantity,
                    "unit_price" => $unit_price,
                    "post_id" => $post_id,
                    "purchase_id" => $purchase_id
                ];
                $return = $this->purchaseContentService->createPurchase($values);
                DB::commit();
                return $return;
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } else {
            throw new HttpException('Forbidden', 403);
        }
    }

    public function deletePurchaseContent(int $id)
    {
        if (Auth::user()->role === "admin"|| Auth::user()->level == 7) {
            return $this->purchaseContentService->deletePurchase($id);
        } else {
            throw new HttpException('Forbidden', 403);
        }
    }

    public function showPaymentsOfUser($id)

    {
        if (Auth::user()->role === 'admin'|| Auth::user()->level == 7) {
            $purchases = $this->purchasesService->findAllByUser($id);
            $total = $this->totalPaymentsService->getTotal($id);
            $rest = $this->totalPaymentsService->getRest($id);
            $deposit = $this->depositService->getSum($id);

            return view('journal/Treasury/PaymentsOfUser', ['purchases' => $purchases, 'total' => $total, 'rest' => $rest, 'deposit' => $deposit]);
        } else {
            return redirect()->back();
        }
    }

    public function showFeedbackOfUser($id)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->level == 5) {
            $feedback = $this->feedbackService->getFeedbackByUserId($id);
            return View('journal/FeedbackOfUser', ['feedback' => $feedback]);
        } else {
            return redirect()->back();
        }
    }

    public function showDepositsOfUser($id)

    {
        if (Auth::user()->role === 'admin'|| Auth::user()->level == 7) {
            $deposits = $this->depositService->findAllByUser($id);
            $userInfo = User::find($id);


            return view('journal/Treasury/DepositsOfUser', ['deposits' => $deposits, 'userInfo' => $userInfo]);
        } else {
            return redirect()->back();
        }
    }

    public function showPaymentDetail($id, Response $response)
    {
        if (Auth::user()->role === 'admin'|| Auth::user()->level == 7 || Auth::user()->level == 5) { 
            $restes = $this->purchasesService->getrestPurchases($id);
            $users = $this->purchasesService->getuserPurchases($id);

            $purchase = $this->purchasesService->find($id);
            if ($purchase !== null) {
                if (Auth::id() === (int)$purchase->user_id || Auth::user()->role === 'admin' || Auth::user()->level == 5) {
                    return view('journal/Treasury/purchase_content', ['purchase' => $purchase, 'restes' => $restes,'users' => $users]);
                } else {
                    $response->setStatusCode(401, 'unauthorized');

                    return redirect('/')->withErrors('Unauthorized');
                }
            } else {
                $response->setStatusCode(404, 'Not Found');

                return redirect('/')->withErrors('Not Found');
            }
        } else {
            return redirect()->back();
        }
    }
    public function PrintshowPaymentDetail($id, Response $response)
    {
        //if (Auth::user()->role === 'admin'||Auth::user()->level == 5 || (int)$purchase->user_id) { 
            $restes = $this->purchasesService->getrestPurchases($id);
            $users = $this->purchasesService->getuserPurchases($id);
            $nets = $this->purchasesService->findNet($id);
            $purchase = $this->purchasesService->find($id);
            if ($purchase !== null) {
                if (Auth::id() === (int)$purchase->user_id || Auth::user()->role === 'admin'|| Auth::user()->level == 5) {
                    return view('journal/Treasury/purchase_content_print', ['purchase' => $purchase, 'restes' => $restes,'users' => $users, 'nets' => $nets]);
                } else {
                    $response->setStatusCode(401, 'unauthorized');

                    return redirect('/')->withErrors('Unauthorized');
                }
            } else {
                $response->setStatusCode(404, 'Not Found');

                return redirect('/')->withErrors('Not Found');
            }
        //} //else {
            //return redirect()->back();
        //}
    }
    public function PrintFactureshowPaymentDetail($id, Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->level == 5) { 
            $restes = $this->purchasesService->getrestPurchases($id);
            $users = $this->purchasesService->getuserPurchases($id);
            $nets = $this->purchasesService->findNet($id);
            $purchase = $this->purchasesService->find($id);
            if ($purchase !== null) {
                if (Auth::id() === (int)$purchase->user_id || Auth::user()->role === 'admin'|| Auth::user()->level == 5) {
                    return view('journal/Treasury/purchase_facture_print', ['purchase' => $purchase, 'restes' => $restes,'users' => $users, 'nets' => $nets]);
                } else {
                    $response->setStatusCode(401, 'unauthorized');

                    return redirect('/')->withErrors('Unauthorized');
                }
            } else {
                $response->setStatusCode(404, 'Not Found');

                return redirect('/')->withErrors('Not Found');
            }
        } else {
            return redirect()->back();
        }
    }
    public function PrintReyonshowPaymentDetail($id, Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->level == 5) { 
            $restes = $this->purchasesService->getrestPurchases($id);
            $users = $this->purchasesService->getuserPurchases($id);
            $nets = $this->purchasesService->findNet($id);
            $purchase = $this->purchasesService->find($id);
            if ($purchase !== null) {
                if (Auth::id() === (int)$purchase->user_id || Auth::user()->role === 'admin'|| Auth::user()->level == 5) {
                    return view('journal/Treasury/purchase_rayon_print', ['purchase' => $purchase, 'restes' => $restes,'users' => $users, 'nets' => $nets]);
                } else {
                    $response->setStatusCode(401, 'unauthorized');

                    return redirect('/')->withErrors('Unauthorized');
                }
            } else {
                $response->setStatusCode(404, 'Not Found');

                return redirect('/')->withErrors('Not Found');
            }
        } else {
            return redirect()->back();
        }
    }
    public function PrintBabdgshowPaymentDetail($id, Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->level == 5) { 
            $restes = $this->purchasesService->getrestPurchases($id);
            $users = $this->purchasesService->getuserPurchases($id);
            $nets = $this->purchasesService->findNet($id);
            $purchase = $this->purchasesService->find($id);
            if ($purchase !== null) {
                if (Auth::id() === (int)$purchase->user_id || Auth::user()->role === 'admin'|| Auth::user()->level == 5) {
                    return view('journal/Treasury/purchase_badge_print', ['purchase' => $purchase, 'restes' => $restes,'users' => $users, 'nets' => $nets]);
                } else {
                    $response->setStatusCode(401, 'unauthorized');

                    return redirect('/')->withErrors('Unauthorized');
                }
            } else {
                $response->setStatusCode(404, 'Not Found');

                return redirect('/')->withErrors('Not Found');
            }
        } else {
            return redirect()->back();
        }
    }

    public function showFeedbackDetail($id)
    {
        if (Auth::user()->role === "admin"||Auth::user()->level == 5) {
            $feedback = $this->feedbackService->getFeedbackById($id);
            return View("journal.Feedback_content", ['feedback' => $feedback]);

        } else {
            return redirect()->back();
        }


    }

    public function showReturnOfUser($id)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->level == 5) {
            $returns = $this->returnsService->getAllByUserId($id);
            return View('journal.ReturnOfUser', ['returns' => $returns]);
        } else {
            return redirect()->back();
        }
    }

    public function showReturnDetail($id)
    {
        if (Auth::user()->role === "admin"||Auth::user()->level == 5) {
            $returnContents = $this->returnContentsService->getContentById($id);
            return View("journal.Return_content", ['returns' => $returnContents]);

        } else {
            return redirect()->back();
        }


    }

    public function getRestOfUser(Request $request): void
    {
        $id = $request->get('id');
        $rest = $this->totalPaymentsService->getRest($id);
        echo($rest);
    } 
    public function getContent(Request $request): void
    {
        $id = $request->get('id');
        $cont = $this->cartContentService->getCart($id);
        echo($cont);
    } 
    public function getPriee(Request $request): void
    {
        $id = $request->get('id');
        $cont = $this->cartContentService->getPricee($id);
        echo($cont);
    } 


    public function changeQuantity(Request $request): void
    {
        if (Auth::user()->role === "admin"||Auth::user()->level == 5) {
            $quantity = $request->get('quantity');
            $product_id = $request->get('product_id');
            $return_id = $request->get('return_id');
            $this->returnContentsService->changeContentByContentIdAndProductId($return_id, $product_id, $quantity);
        }
    }

    public function deleteProduct(Request $request): void
    {
        if (Auth::user()->role === "admin"||Auth::user()->level == 5) {

            $product_id = $request->get('product_id');
            $return_id = $request->get('return_id');
            $this->returnContentsService->deleteContentByContentIdAndProductId($return_id, $product_id);
        }
    }

    public function createReturn()
    {
        if (Auth::user()->role === 'admin'||Auth::user()->level == 5) {

//            $users=User::all()
//                ->where('role','!=','admin')
//                ->where('role','!=','Comptoir');
            $users = $this->purchasesService->getUsersHowPurchased();
            $posts=Post::query()->select('*')->groupBy('nom_comr')->get();

            return View('journal.createReturn', ['users' => $users,'posts'=>$posts]);
        } else {
            return redirect()->back();
        }
    }

    public function getProductsForUser(Request $request): void
    {
        $user_id = (int)$request->get('user_id');
        $purchases = $this->purchasesService->getAllRecordsByUser($user_id);
        $posts = Post::query()->select("*")->where("qte", '>',0 )->get();
        $output = "";
        $out = "";
        if (count($purchases) > 0) {
            $output .= '<div class="wrap-table-shopping-cart bgwhite" id="DailyReport">
                          <table class="table-shopping-cart" id="DailyReport">
                              <tr>
                                  <th class="column-1">ID</th>
                                  <th class="column-2">N.Facture</th>
                                  <th class="column-2">Date</th>
                                  <th class="column-2">Produits</th>
                                  <th class="column-2">QTE</th>
                                  <th class="column-4 p-l-70">Action </th>
                              </tr>';

            foreach ($purchases as $content) {
                $output .= '<tr class="table-row" id="DailyReport">
                                      <input type="hidden" name="id" id="id" value="' . $content->post->id . '">
                                      <td class="column-1">' . $content->post->id . '</td>
                                      <td class="column-1">' . $content->purchase_id . '</td>
                                      <td class="column-1">' . $content->created_at . '</td>
                                      <td class="column-2"><a href="/Posts/' . $content->post->id . '" class="link">' . $content->post->nom_comr . '</a></td>
                                      <td class="column-1">' . $content->quantity . '</td>

                                      <td class="column-4 p-l-70"><button class="btn btn-success btnAdd"> Ajouter </button></td>

                                  </tr>';
            }


            $output .= "</table></div>";

        }
        echo $output;/*
        if (count($posts) > 0) {
            $out .= '<div class="wrap-table-shopping-cart bgwhite">
                          <table class="table-shopping-cart" id="DailyReport">
                              <tr>
                                  <th class="column-1">ID</th>
                                  <th class="column-2">Produits</th>
                                  <th class="column-4 p-l-70">Action </th>
                              </tr>';

            foreach ($posts as $content) {
                $out .= '<tr class="table-row">
                                      <input type="hidden" name="id" id="id" value="' . $content->id . '">
                                      <td class="column-1">' . $content->id . '</td>

                                      <td class="column-2"><a href="/Posts/' . $content->id . '" class="link">' . $content->nom_comr . '</a></td>

                                      <td class="column-4 p-l-70"><button class="btn btn-success btnAdd"> Ajouter </button></td>

                                  </tr>';
            }


            $out .= "</table></div>";

        }
        echo $out;

*/


    }

    public function addToBonCommand(Request $request)
    {
        $post = Post::find($request->get('postId'));
        $command = new Boncommand();
        $command->post_id = $request->get('postId');
        $command->qt = $request->get('postId'); //qt bot nullable
        $command->created_at = Carbon::now();
        $command->save();
        $td = '<tr>
                    <td><input type="checkbox" name="" id=""></td>
                    <td>' . $post->nom_comr . '</td>
                    <td>' . Carbon::now()->diffForHumans() . '</td>
            </tr>';
        echo $td;
    }
    public function updateColi(Request $request)
    {
        $purchase_id = $request->get('purchase_id');
        $coli = $request->get('coli');
        
        Purchases::where("id", $purchase_id)->update(['coli' => $coli]);
    }
    public function updateDiscount(Request $request) 
    {
        $purchase_id = $request->get('purchase_id');
        $total_net = $request->get('total_net');
        $total_disc = $request->get('remise');
        
        Purchases::where("id", $purchase_id)->update(['remise' => $total_disc, 'total_net' => $total_net]);
        Purchases::where("id",'=',$request->get("id"))->update(['total_price' => DB::raw('total_price - ' . $total_disc . '')]);
        TotalPayments::where("user_id",'=',$request->get("user_id"))->update(['rest' => DB::raw('rest - ' . $total_disc . '')]);
        TotalPayments::where("user_id",'=',$request->get("user_id"))->update(['total_amount' => DB::raw('total_amount - ' . $total_disc . '')]);

        echo $total_disc;
    }
    public function updateDiscountRese(Request $request)
    {
        $user_id = $request->get('user_id');
        $total_net = $request->get('total_net');
        $total_disc = $request->get('remise');

        User::where("id", $user_id)->update(['credit' => $total_disc, 'rese' => $total_net]);
    }
    public function addComment(Request $request)
    {
        PurchaseContent::where('id', $request->get('id'))->update(['coment' => $request->get('comment')]);
    }

    public function comptPurchase()
    {


    } 

    public function showcomptPurchase(Response $response)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->level == 5|| Auth::user()->level == 7) {
            //  $total_rest = $this->totalPaymentsService->totalRest();
            $users = User::all()->where("role", '!=', 'admin')->where("role", '!=', 'Comptoir');
            return view('journal.comptPurchase', ['users' => $users]);

        }
    }

    public function saveComptPurchase(Request $request)
    {

        try {
            DB::beginTransaction();
            $user=User::where('id',$request->get('user_id'))->first();
            $purchase = new Purchases();
            $purchase->user_id=$user->id;
            $purchase->total_price=$request->get('Total_price');
            $purchase->remise=$request->get('RemiseTotal');
            $purchase->total_net=$request->get('Total_net');
            $purchase->payment_method = (int)$request->input('payment_method');
            $purchase->save();
            $totalPrice = 0;
            $inputs = $request->input('data');
            foreach ($inputs as $input) {
                    $post = Post::find($input['post_id']);
                    /*if ($post->qte < $input['qte']) {
                        throw new Exception('invalid quantity');
                    }*/

                    //price update marge
                    $unitPrice = $post->pv_ht;
 
                    if( $user->option == 4){
                        $unitPrice=$post->marge3;
                    }else if( $user->option == 3){
                        $unitPrice = $post->marge2;
                    }else if( $user->option == 2){
                        $unitPrice=$post->marge1;
                    }

                    $purchaseContent = $this->purchaseContentService->createPurchase([
                        'purchase_id' => $purchase->id,
                        'post_id' => $post->id,
                        'quantity' => $input['qte'],
                        'unit_price'=>  $unitPrice,
                        'price' => (float)($unitPrice * $input['qte']),//change to $unitPrice
                        'type' => 'post',
                    ]);

                    Post::where('id',$post->id)->update([

                        'qte' => $post->qte - $input['qte']
                        ]);

                  $totalPrice += (float)$purchaseContent['purchase_content']->price;
                    $this->postService->UpdatePost($post->id, [
                        'qte' => $post->qte - $input['qte'],
                        'sold' => $post->sold + $input['qte'],
                    ]);

            }
            $this->purchasesService->UpdatePurchase($purchase->id, ['total_price' => $totalPrice]);

            if (DB::table('total_payments')->where('user_id', $user->id)->count() > 0) {
                DB::table('total_payments')->where('user_id', $user->id)->update([
                    'total_amount' => DB::raw('total_amount + '. (float)$totalPrice .''),
                ]);
                DB::table('total_payments')->where('user_id', $user->id)->update([
                    'rest' => DB::raw('rest + '. (float)$totalPrice .''),
                ]);
            } else {
                $total_payments = new TotalPayments();
                $total_payments->user_id = $user->id;
                $total_payments->total_amount = (float)$totalPrice;
                $total_payments->rest = (float)$totalPrice;
                $total_payments->save();
            }
            //return redirect()->route('Invoices.index');
            DB::commit();
            

        } catch (Exception | Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return redirect()->route('Invoices.index');
    }
    public function saveCompt(Request $request)
    {

        try {
            DB::beginTransaction();
            $user=User::where('id',$request->get('user_id'))->first();
            $purchase = new Purchases();
            $purchase->user_id=$user->id;
            $purchase->total_price=$request->get('Total_price');
            $purchase->remise=$request->get('RemiseTotal');
            $purchase->total_net=$request->get('Total_net');
            $purchase->payment_method = (int)$request->input('payment_method');
            $purchase->save();
            $totalPrice = 0;
            $inputs = $request->input('data');
            foreach ($inputs as $input) {
                    $post = Post::find($input['post_id']);
                    if ($post->qte < $input['qte']) {
                        throw new Exception('invalid quantity');
                    }

                    //price update marge
                    $unitPrice = $post->pv_ht;
 
                    if( $user->option == 4){
                        $unitPrice=$post->marge3;
                    }else if( $user->option == 3){
                        $unitPrice = $post->marge2;
                    }else if( $user->option == 2){
                        $unitPrice=$post->marge1;
                    }

                    $purchaseContent = $this->purchaseContentService->createPurchase([
                        'purchase_id' => $purchase->id,
                        'post_id' => $post->id,
                        'quantity' => $input['qte'],
                        'unit_price'=>  $unitPrice,
                        'price' => (float)($unitPrice * $input['qte']),//change to $unitPrice
                        'type' => 'post',
                    ]);

                    Post::where('id',$post->id)->update([

                        'qte' => $post->qte - $input['qte']
                        ]);

                  $totalPrice += (float)$purchaseContent['purchase_content']->price;
                    $this->postService->UpdatePost($post->id, [
                        'qte' => $post->qte - $input['qte'],
                        'sold' => $post->sold + $input['qte'],
                    ]);

            }
            $this->purchasesService->UpdatePurchase($purchase->id, ['total_price' => $totalPrice]);

            if (DB::table('total_payments')->where('user_id', $user->id)->count() > 0) {
                DB::table('total_payments')->where('user_id', $user->id)->update([
                    'total_amount' => DB::raw('total_amount + '. (float)$totalPrice .''),
                ]);
                DB::table('total_payments')->where('user_id', $user->id)->update([
                    'rest' => DB::raw('rest + '. (float)$totalPrice .''),
                ]);
            } else {
                $total_payments = new TotalPayments();
                $total_payments->user_id = $user->id;
                $total_payments->total_amount = (float)$totalPrice;
                $total_payments->rest = (float)$totalPrice;
                $total_payments->save();
            }
            foreach ($this->cartService->getCartInfo()->cartContents as $content) {
                $this->cartContentService->deleteOne($content->id);
            }

            //return redirect()->route('Invoices.index');
            DB::commit();
            

        } catch (Exception | Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        return redirect()->route('Invoices.index');
    }
}

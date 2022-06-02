<?php

namespace App\Http\Controllers;
use Exception;
use App\ProviderReturn;
use App\ProviderReturnContent;
use App\Services\fournisseurService;
use App\Services\InvoicesService;
use App\Services\PostService;
use App\Services\ProviderReturnContentService;
use App\Services\ProviderReturnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Fournisseur;
use App\Models\Post;
use Illuminate\Support\Facades\DB;


class ProviderReturnController extends Controller
{
    private $providerReturnService;

    private $providerReturnContentService;
    private $fournisseurService;
    private  $invoicesService;
    /**
     * @var PostService
     */
    private $postService;
    public function __construct(ProviderReturnService $providerReturnService,
    ProviderReturnContentService $providerReturnContentService ,
    InvoicesService $invoicesService,
    fournisseurService $fournisseurService,
    PostService $postService)

    {
        $this->providerReturnService=$providerReturnService;
        $this->providerReturnContentService=$providerReturnContentService;
        $this->fournisseurService=$fournisseurService;
        $this->invoicesService=$invoicesService;
        $this->postService=$postService;

    }

    public function showReturnOfUser($id)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
            $returns = $this->providerReturnService->getAllByUserId($id);
            return View('ProviderReturn.ReturnOfUser', ['returns' => $returns]);
        } else {
            return redirect()->back();
        }
    }
    public function showReturnDetail($id)
    {
        if (Auth::user()->role === "admin"||Auth::user()->role === 'Comptoir') {
            $returnContents = $this->providerReturnContentService->getContentById($id);
            return View("ProviderReturn.Return_content", ['returns' => $returnContents]);

        } else {
            return redirect()->back();
        }


    }
    public function store(Request $request){

        try {
            DB::beginTransaction();
            $provider=Fournisseur::where('id',$request->get('provider_id'))->first();

           // $return=$this->providerReturnService->create(Auth::id());
            $inputs = $request->input('data');
            $total=0;
            foreach ($inputs as $input) {
                $purchaseQuantity=$this->fournisseurService->findQuantityByPostId($input['postId']);
                $quantity=$input['quantity'];
                $total+=(float)$this->postService->find($input['postId'])->prix*$quantity;
                 $post = Post::query()->where('id','=',$input['postId'])->first();
                $this->postService->UpdatePost($post->id, [
                    'qte' => $post->qte - $input['quantity'],
                ]);
                }
                $return=$this->providerReturnService->create($request->get('provider_id'),$total);
                foreach ($inputs as $input){
                    $purchaseQuantity=$this->fournisseurService->findQuantityByPostId($input['postId']);
                $quantity=$input['quantity'];
                if ($quantity<$purchaseQuantity ){

                    throw new Exception('invalid quantity'); 
                }
                $returnContent=new ProviderReturnContent();
                $returnContent->return_id=$return->id;
                $returnContent->post_id=$input['postId'];
                $returnContent->quantity=$quantity;
                $returnContent->save();

            }
            DB::table('total_payment_providers')->where('id','=',$request->get('provider_id'))->update(
                [
                    'rest' => DB::raw('rest - '. (float)$total .''),
                ]

            );
            DB::commit();

        } catch (Exception | Throwable $e) {

            throw $e;
        }
    }
    public function indexOfReturn(Request $request)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {

            $returns = $this->providerReturnService->getAllWithTotal();
            return view("ProviderReturn.Return_journal")->with("returns", $returns);
        } else {
            return redirect()->back();
        }
    }
    public function createReturn()
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {

//            $users=User::all()
//                ->where('role','!=','admin')
//                ->where('role','!=','Comptoir');
            $providers = $this->fournisseurService->getProvidersHowPurchased();

            return View('ProviderReturn.createReturn', ['providers' => $providers]);
        } else {
            return redirect()->back();
        }
    }
    public function confirm(Request $request){
        if (Auth::user()->role==='admin'||Auth::user()->role === 'Comptoir') {
            $returnId = $request->get('returnId');
            $return = ProviderReturnContent::query()->where('return_id', '=', $returnId)->first();
            $return->confirmed = true;
            $return->save();
        }
    }
    public function changeQuantity(Request $request): void
    {
        if (Auth::user()->role === "admin"||Auth::user()->role === 'Comptoir') {
            $quantity = $request->get('quantity');
            $product_id = $request->get('product_id');
            $return_id = $request->get('return_id');
            $this->providerReturnContentService->changeContentByContentIdAndProductId($return_id, $product_id, $quantity);
        }
    }
    public function deleteProduct(Request $request): void
    {
        if (Auth::user()->role === "admin"||Auth::user()->role === 'Comptoir') {

            $product_id = $request->get('product_id');
            $return_id = $request->get('return_id');
            $this->providerReturnContentService->deleteContentByContentIdAndProductId($return_id, $product_id);
        }
    }

    public function getProductsForUser(Request $request): void
    {
        $provider_id = (int)$request->get('provider_id');
        $purchases = $this->invoicesService->getAllRecordsByProvider($provider_id);
        $output = "";
        if (count($purchases) > 0) {
            $output .= '<div class="wrap-table-shopping-cart bgwhite">
                          <table class="table-shopping-cart">
                              <tr class="table-head">
                                  <th class="column-1">ID</th>
                                  <th class="column-2">Produits</th>
                                  <th class="column-4 p-l-70">Action </th>
                              </tr>';

            foreach ($purchases as $content) {
                $output .= '<tr class="table-row">
                                      <input type="hidden" name="id" id="id" value="' . $content->post_id . '">
                                      <td class="column-1">' . $content->id . '</td>

                                      <td class="column-2"><a href="/Posts/' . $content->_id . '" class="link">' . $content->com_name . '</a></td>

                                      <td class="column-4 p-l-70"><button class="btn btn-success btnAdd"> Ajouter </button></td>

                                  </tr>';
            }


            $output .= "</table></div>";

        }
        echo $output;


    }
}

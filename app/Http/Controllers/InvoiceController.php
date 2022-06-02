<?php

namespace App\Http\Controllers;
use App\Models\Post;
use App\Fournisseur;
use App\Invoice;
use App\InvoiceRef;
use App\InvoicesRef; 
use App\Models\Categorie;
use App\ProviderPayment;
use App\Services\fournisseurService;
use App\Services\InvoicesService;
use App\Services\PaymentsProviderService;
use App\Services\PostService;
use App\Services\TotalPaymentProviderService;
use Carbon\Carbon;
use HttpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Application;


class InvoiceController extends Controller
{
    private $fournisseurService;
    private $postService;
    private $invoicesService;
    /**
     * @var TotalPaymentProviderService
     */
    private $totalPaymentProviderService;
    /**
     * @var PaymentsProviderService
     */
    private $paymentsProviderService;

    public function __construct(PaymentsProviderService $paymentsProviderService, TotalPaymentProviderService $totalPaymentProviderService, fournisseurService $fournisseurService, InvoicesService $invoicesService, PostService $postService)
    {
        $this->fournisseurService = $fournisseurService;
        $this->postService = $postService;
        $this->invoicesService = $invoicesService;
        $this->totalPaymentProviderService = $totalPaymentProviderService;
        $this->paymentsProviderService = $paymentsProviderService;
    }

    public function index()
    {
        $invoices = $this
            ->invoicesService
            ->getAll();
        $providers = $this
            ->fournisseurService
            ->getAll();
        return view('Invoices.index', ['invoices' => $invoices, 'providers' => $providers]);
    }

    public function create()
    {
        $list2 = Categorie::all()->pluck('categorie', 'id');
        return view('Invoices.create')
            ->with(['list2' => $list2]);

    }

    public function store(Request $request)
    { 
        

        $invoiceRef = new InvoicesRef();
        $inputs = $request->input('data');
        $invoiceRef->created_date = $request->get('crated_date');
        $invoiceRef->provider_id = $request->get('providerId');
        $invoiceRef->remise = $request->get('remise');
        $invoiceRef->total_net = $request->get('total_net');
        $invoiceRef->total_h_t = $request->get('total_h_t');
        $invoiceRef->num_invoice = $request->get('num_invoice');
        $invoiceRef->save();

        foreach ($inputs as $input) {
            $invoice = new Post();
            $invoice->inventory=0;
            $invoice->ref_invoice_id = $invoiceRef->id;
            $invoice->nom_comr = $input['nom_comr'];
            $invoice->level = $input['level'];
            $invoice->dosage = $input['emplacem'];
            $invoice->type = $input['type'];
            $invoice->qte = $input['qte'];
            $invoice->inventory = $input['qte'];
            $invoice->date_perm = $input['date_perm'];
            $invoice->pv_ht = $input['pv_ht'];
            $invoice->prix = $input['prix'];
            $invoice->sold = $input['ppa']; 
            $invoice->qvip = $input['qvip']; 
            $invoice->qplusimport = $input['qplusimport']; 
            $invoice->qimport = $input['qimport']; 
            $invoice->qord = $input['qord']; 
            $invoice->marge1 = $input['marge_1']; 
            $invoice->marge2 = $input['marge_2']; 
            $invoice->marge3 = $input['marge_3']; 
            $invoice->CTherapeutique = $input['qteug']; 
            $invoice->nom_dci = $input['qtec']; 
            $invoice->tag = $input['tag'];
            $invoice->Conditionnement = $input['Conditionnement'];
            $invoice->categorie_id = $input['categori'];
            $invoice->offre = $input['offre'];
            $invoice->cover_image =$input['image'];
            $invoice->save();
        }
        DB::table('total_payment_providers')
                ->where('provider_id', $invoiceRef->provider_id)->update(['rest' => DB::raw('rest + ' . $invoiceRef->total_net . '')]);
        return redirect()
            ->route('Invoices.index');
    }

    public function show(int $id)
    {
        $invoices = $this
            ->invoicesService
            ->getById($id);
        $num_invoice = InvoicesRef::where('id', '=', $id)->firstOrFail();
        return View('Invoices/show', ['invoices' => $invoices, 'num_invoice' => $num_invoice->num_invoice]);

    }

    public function searchProvider(Request $request)
    {

        $name = $request->get('name');
        $providers = $this
            ->fournisseurService
            ->getProviderByName($name);
        $output = '<ul class="dropdown-menu providerContent text-center " style="display:block;position: static">';
        if (count($providers) > 0) {
            foreach ($providers as $provider) {
                $output .= '<li><a data-id="' . $provider->id . '" href="#">' . $provider->name . '</a></li>';
            }

        } else {
            $output .= '<p class="text-center" style="padding: 2px 5px">Aucun Utilisateur à Afficher.</p>';
        }
        $output .= '</ul>';
        echo $output;

    }

    public function searchPost(Request $request): void
    {

        $name = $request->get('name');
        $posts = $this
            ->postService
            ->findPostsByNomComrForInvoice($name);
        $output = '<div><ul class="postContent dropdown-menu text-center " style="display:block;position: relative;margin-left: 100%;max-height: 200px;overflow-y: auto;" >';
        if (count($posts) > 0) {
            foreach ($posts as $post) {
                $output .= '<li style="z-index: 2;padding: 5px 0"><a data-toggle="tooltip" data-html="true" title="nom:' . $post->nom_comr . ' &#013;qte: ' . $post->qte . ' &#013;prix: ' . $post->prix . ' &#013;PPA: ' . $post->sold . ' &#013;date peremption: ' . $post->date_perm . '" data-id="' . $post->id . '"href="#">' . $post->nom_comr . '</a></li>';
            }

        } else {
            $output .= '<p class="text-center" style="padding: 2px 5px">Aucun Utilisateur à Afficher.</p>';
        }
        $output .= '</ul></div>';
        echo $output;

    }

    public function getSugProductInfo(Request $request)
    {

        $productSugId = $request->get('productSugId');
        $product = $this
            ->postService
            ->findForSug($productSugId);
        $data = array(
            'post_id' => $product->id,
            'nom_comr' => $product->nom_comr,
            'prix' => $product->prix,
            'qte' => $product->qte,
            'date_perm' => Carbon::parse($product->date_perm)
                ->format('Y-m-d'),
            'pv_ht' => $product->pv_ht,
            'categorie_id' => $product->categorie_id,
            'tag' => $product->tag,
            'offre' => $product->offre,
            'Conditionnement' => $product->Conditionnement,
            'image' => $product->cover_image,
            'ppa' => $product->sold,
            'remise' => $product->remise,
            'qtec' => $product->nom_dci,
            'emplacement' => $product->dosage

        );
        return response()
            ->json(['data' => $data]);

    }

    public function getRest(Request $request): void
    {
        $id = $request->get('id');
        $rest = $this
            ->totalPaymentProviderService
            ->getRest($id);
        echo($rest);
    }

    public function applyPayment(Request $request): void
    {

        $amount = (float)$request->get('amount');

        $provider = $request->get('provider_id');
        $deposit = new ProviderPayment();
        $deposit->provider_id = $provider;
        $deposit->amount = $amount;

        if ($deposit->save()) {

            DB::table('total_payment_providers')
                ->where('provider_id', $provider)->update(['rest' => DB::raw('rest - ' . $amount . '')]);
        }

    }

    public function showPayments()
    {
        $payments = $this
            ->paymentsProviderService
            ->getAll();
        $providers = $this
            ->fournisseurService
            ->getAll();
        return View('journal/Treasury/payments_provider_journal', ['payments' => $payments, 'providers' => $providers]);

    }

    public function deletePaymentById(Request $request)
    {
        $id = $request->get('id');
        $amount = (float)$request->get('amount');
        $provider_id = $request->get('provider_id');
        $this->totalPaymentProviderService->deletePaymentById($id, $amount, $provider_id);
    }

    public function modifyPaymentById(Request $request)
    {
        $id = $request->get('id');
        $amount = (float)$request->get('amount');
        $provider_id = $request->get('provider_id');
        $this->totalPaymentProviderService->modifyPaymentById($id, $amount, $provider_id);

    }

    public function SearchPayment(Request $request): void
    {
        $providerName = $request->get('providerName');
        $rows = $this
            ->paymentsProviderService
            ->findAllByUserName($providerName);
        $output = '';
        $i = 1;
        if (count($rows) > 0) {

            foreach ($rows as $row) {
                $output .= '  <tr class="table-row">
                                <td class="column-1">' . $i . '</td>
                                <td class="column-2"><a href="PaymentsOfProvider/' . $row->provider_id . '">' . $row->name . '</a></td>
                                <td class="column-3">' . $row->sum . ' DA </td>
                            </tr>';
                $i++;
            }

        } else {
            $output = 'Nothing to show';
        }
        echo $output;
    }

    public function showPaymentsOfProvider($id)
    {

        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {
            $payments = $this
                ->paymentsProviderService
                ->findAllByUser($id);
            $userInfo = Fournisseur::find($id);

            return view('journal/Treasury/PaymentsOfProvider', ['payments' => $payments, 'userInfo' => $userInfo]);
        }

        return redirect()->back();
    }

    public function edit($id)
    {
        $invoiceRef = InvoicesRef::find($id);
        $invoice = Invoice::find($id);

        return view('Invoices.edit', ['invoiceRef' => $invoiceRef, 'invoice' => $invoice]);
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role === 'admin'||Auth::user()->role === 'Comptoir') {

            $invoiceRef = InvoicesRef::find($id);
            $inputs = $request->input('data');
            $invoiceRef->created_date = $request->input('crated_date');
            $invoiceRef->provider_id = $request->input('providerId');
            $invoiceRef->remise = $request->input('remise');
            $invoiceRef->total_net = $request->input('total_net');
            $invoiceRef->total_h_t = $request->input('total_h_t');
            $invoiceRef->num_invoice = $request->input('num_invoice');
            $invoiceRef->save();

            foreach ($inputs as $input) {
                $invoice = Invoice::find($id);
                $invoice->ref_invoice_id = $invoiceRef->id;
                $invoice->com_name = $input['nom_comr'];
                $invoice->post_id = $input['post_id'];
                $invoice->quantity = $input['qte'];
                $invoice->date_perm = $input['date_perm'];
                $invoice->pv_ht = $input['pv_ht'];
                $invoice->ppa = $input['prix'];
                $invoice->tag = $input['tag'];
                $invoice->Conditionnement = $input['Conditionnement'];
                $invoice->cart_id = $input['categorie'];
                $invoice->offre = $input['offre'];
                $invoice->image = $input['image'];
                $invoice->save();
            }
        }

        return redirect('/fourniseurs')->with('succecess', 'fournisseur updated');
    }

    function inde()
    {
        $data = DB::table('invoices')
//        ->where('ref_invoices_id','=',$id)
            ->get();

        return view('table_edit', compact('data'));
    }

    function action(Request $request)
    {

        if ($request->ajax()) {
            if ($request->action == 'edit') {
                $data = array(
                    'com_name' => $request->com_name,
                    'date_perm' => $request->date_perm,
                    'pv_ht' => $request->pv_ht,
                    'ppa' => $request->ppa,
                    'quantity' => $request->quantity,
                    'tag' => $request->tag,
                    'Conditionnement' => $request->Conditionnement

                );
                DB::table('invoices')
                    ->where('id', $request->id)
                    ->update($data);
            }
            if ($request->action == 'delete') {
                DB::table('invoices')
                    ->where('id', $request->id)
                    ->delete();
            }
            return response()->json($request);
        }
    }

    public function updatePurchaseContents(int $id, Request $request)
    {
        if (Auth::user()->role === "admin"||Auth::user()->role === 'Comptoir') {
            $quantity = $request->input('quantity');
            $unit_price = $request->input('unit_price');
            $values = [
                "quantity" => $quantity,
                "unit_price" => $unit_price
            ];
            return $this->purchaseContentService->updatePurchase($id, $values);
        } else {
            throw new HttpException('Forbidden', 403);
        }
    }

    public function updateAttr(Request $request)
    {
        Invoice::where('id', '=', $request->get('id'))->update([$request->get('type') => $request->get('value')]);
        echo 'success';
        
             
    }

    public function updateTotalNet(Request $request)
    {
        InvoicesRef::where('id',$request->get('id'))->update(['total_net'=>$request->get('total_net'),'total_h_t'=>$request->get('total_price'),'remise'=>$request->get('remise')]);
            echo 'success';
    }
    public function updatePurchaseContentsss(int $id, Request $request)
    {
        
            $inventory = $request->input('inventory');
            $qte = $request->input('qte');
            $prix = $request->input('prix');
            $pv_ht = $request->input('pv_ht');
            $values = [
                "inventory" => $inventory,
                "qte" =>$qte,

                "prix" => $prix,
                "pv_ht"=>$pv_ht
            ];
            return $this->postService->updatePurchase($id, $values);
        
    }
    public function deleteFacture(Request $request)
    {

        $id=$request->get('id');
        $total_price= (float)$request->get('total_net');
        $user_id= $request->get('provider_id');
        $this->invoicesService->modifyDeposittById($id,$total_price,$user_id);
    }
    
}


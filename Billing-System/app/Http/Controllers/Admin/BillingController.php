<?php

namespace Pterodactyl\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Pterodactyl\Models\Nest;
use Pterodactyl\Models\Node;
use Pterodactyl\Models\Invoice;
use Pterodactyl\Models\Category;
use Pterodactyl\Models\Product;
use Pterodactyl\Models\PromotionalCode;
use Pterodactyl\Models\User;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Contracts\View\Factory;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File; 
use Validator;

class BillingController extends Controller
{
    /**
     * @var \Illuminate\Contracts\View\Factory
     */
    private $view;

    /**
     * @var \Pterodactyl\Repositories\Eloquent\NodeRepository
     */
    private $alert;

    /**
     * NodeController constructor.
     *
     * @param \Pterodactyl\Repositories\Eloquent\NodeRepository $repository
     * @param \Illuminate\Contracts\View\Factory $view
     */

    public function __construct(AlertsMessageBag $alert, Factory $view)
    {
        $this->view = $view;
        $this->alert = $alert;
    }


    public function index(Request $request)
    {

        $this_month_income = Invoice::select(DB::raw("SUM(amount) AS amount"))
        ->where("reason", "=", "Top up Credit")
        ->groupBy(DB::raw("YEAR(created_at) DESC, MONTH(created_at) DESC"))
        ->sum("amount");

        $this_year_income = Invoice::select(DB::raw("SUM(amount) AS amount"))
        ->where("reason", "=", "Top up Credit")
        ->groupBy(DB::raw("YEAR(created_at) DESC"))
        ->sum("amount");

        $alltime_income = Invoice::select(DB::raw("SUM(amount) AS amount"))
        ->where("reason", "=", "Top up Credit")
        ->sum("amount");

        $income_month_graph = Invoice::select(DB::raw('SUM(amount) AS amount, MONTH(created_at) AS month, CONCAT(\'#\', LEFT(MD5(MONTH(created_at)), 6)) AS color'))
            //->where(DB::raw('YEAR(NOW()) = YEAR(created_at)'))
            ->groupBy(DB::raw("YEAR(created_at) ASC, MONTH(created_at) ASC"))
            ->get();

        $income_country_graph = Invoice::select(DB::raw('SUM(amount) AS amount, billing_country, CONCAT(\'#\', LEFT(MD5(billing_country), 6)) AS color'))
            //->where(DB::raw('YEAR(NOW()) = YEAR(created_at)'))
            ->groupBy('billing_country')
            ->get();

        $user_accounts = DB::table("users")->sum("balance");

        $billing = DB::table("billing")->get();

        return view("admin.billing.index", [
            "this_month_income" => $this_month_income, 
            "this_year_income" => $this_year_income, 
            "alltime_income" => $alltime_income,
            "user_accounts" => $user_accounts, 
            "billing" => $billing, 
        ])->with('income_month_graph', $income_month_graph)
          ->with('income_country_graph', $income_country_graph);
    }

    /**
     * Tos
     */

    public function tos()
    {
        $tos = DB::table("billing")->get();
        return view("admin.billing.tos.index", [
            "tos" => $tos[0]->tos
        ]);
    }
    public function tos_update(Request $request)
    {
        DB::table("billing")->update([
            "tos" => $request->tos
        ]);

        $this->alert->success('You have successfully updated the tos.')->flash();
        return redirect()->back();
    }

    /**
     * Settings
     */

    public function settings()
    {

        $billing = DB::table("billing")->get();

        return view("admin.billing.settings.index", [
            "billing" => $billing
        ]);
    }

    public function settings_sotre(Request $request)
    {

        if ($request->currency == 'euro') {
            $currency_code = 'EUR';
        } elseif ($request->currency == 'pound') {
            $currency_code = 'GBP';
        } else {
            $currency_code = 'USD';
        }

        DB::table("billing")->update([
            "currency" => $request->currency, 
            "currency_code" => $currency_code, 
            "categories_img" => $request->categories_img,
            "categories_img_width" => $request->categories_img_width,
            "categories_img_height" => $request->categories_img_height,
            "categories_img_rounded" => $request->categories_img_rounded,
            "products_img" => $request->products_img,
            "products_img_width" => $request->products_img_width,
            "products_img_height" => $request->products_img_height,
            "products_img_rounded" => $request->products_img_rounded,
            "use_categories" => $request->use_categories,
            "use_products" => $request->use_products,
            "use_deploy" => $request->use_deploy,
        ]);

        $this->alert->success('You have successfully updated the settings.')->flash();
        return redirect()->back();
    }

    /**
     * Invoices
     */
    public function validateBilling($user)
    {
        if (!$user->billing_first_name) return false;
        if (!$user->billing_last_name) return false;
        if (!$user->billing_address) return false;
        if (!$user->billing_city) return false;
        if (!$user->billing_country) return false;
        if (!$user->billing_zip) return false;
        return true;
    }

    public function invoices()
    {
        $invoices = Invoice::orderBy("id", "desc")->where("reason", "=", "Top up Credit")->paginate(15);
        $billing = DB::table("billing")->get();

        return view("admin.billing.invoices.index", [
            "invoices" => $invoices,
            "billing" => $billing
        ]);
    }
    public function invoices_new(Request $request)
    {
        $billing = DB::table("billing")->get();

        return view("admin.billing.invoices.new", [
            "billing" => $billing
        ]);
    }
    public function invoices_pdf(Request $request)
    {
        return Invoice::find($request->id)->downloadPdf();
    }
    public function invoices_store(Request $request)
    {
        $request->validate([
            "user_id" => "required|exists:users,id", 
            "amount" => "required|numeric|min:-500|max:500",
        ]);

        $user = User::find($request->user_id);

        if (!$this->validateBilling($request->user())) {
            return redirect()->back()->withErrors("You need to fill up your billing info before making any payments.");

        }

        $invoice = new Invoice();
        $invoice->amount = $request->amount;
        $invoice->reason = "Top up Credit";
        $invoice->user_id = $request->user()->id;
        $invoice->billing_first_name = $request->user()->billing_first_name;
        $invoice->billing_last_name = $request->user()->billing_last_name;
        $invoice->billing_address = $request->user()->billing_address;
        $invoice->billing_city = $request->user()->billing_city;
        $invoice->billing_country = $request->user()->billing_country;
        $invoice->billing_zip = $request->user()->billing_zip;
        $invoice->save();

        $this->alert->success('You have successfully Added the invoice.')->flash();

        return redirect(route("admin.billing.invoices"));
    }

    /**
     * Gateways
     */

    public function gateways()
    {
        $billing = DB::table("billing")->get();
        $gateways = DB::table("gateways")->get();
        if (count($gateways) == 0) {
            $gateways == "none";
        }
        return view("admin.billing.gateways.gateways", [
            "gateways" => $gateways
        ]);
    }

    public function gateways_edit($gateway)
    {

        $gateways = DB::table("gateways")->where("gateway", "=", $gateway)->get();

        return view("admin.billing.gateways.gateway", [
            "gateways" => $gateways
        ]);
    }

    public function gateways_store($gateway, Request $request)
    {

        $api = $_REQUEST["api"];
        $private_key = $_REQUEST["private_key"];
        $mode = $_REQUEST["mode"];

        DB::table("gateways")->where("gateway", "=", $gateway)->update([
            "api" => $api, 
            "private_key" => $private_key, 
            "mode" => $mode
        ]);

        $this->alert->success('You have successfully Updated the PayPal Settings.')->flash();
        return redirect(route("admin.billing.payoptions"));
    }

    public function gateways_activate($gateway)
    {
        DB::table("gateways")->where("gateway", "=", $gateway)->update([
            "enabled" => 1
        ]);
        $this->alert->success('You have successfully Enabled the PayPal.')->flash();
        return redirect(route("admin.billing.payoptions"));
    }

    public function gateways_deactivate($gateway)
    {
        DB::table("gateways")->where("gateway", "=", $gateway)->update([
            "enabled" => 0
        ]);
        $this->alert->success('You have successfully Disabled the PayPal.')->flash();
        return redirect(route("admin.billing.payoptions"));
    }

    /**
     * Categories
     */

    public function categories(Request $request)
    {

        $billing = DB::table("billing")->get();
        $categories = QueryBuilder::for(
            Category::query()
        )
            ->allowedFilters(['name', 'description'])
            ->allowedSorts(['id'])
            ->paginate(25);


        return $this->view->make('admin.billing.categories.index', [
            'categories' => $categories,
            'billing' => $billing
        ]);
    }

    public function categories_new()
    {
        $billing = DB::table("billing")->get();
        $categories = DB::table("categories")->get();
        return view("admin.billing.categories.new", [
            "categories" => $categories
        ]);
    }

    public function categories_store(Request $request)
    {
        $request->validate([
            "select_file"  => "nullable|image|mimes:jpg,jpeg,png,gif|max:2048",
            "name" => "required", 
            "priority" => "required", 
            "description" => "required", 
            "visible" => "required"
        ]);

        $img_path = "/uploads/categories";
        $img = $request->file('select_file');


        $category = new Category;
        $category->name = $request->name;
        $category->priority = $request->priority;
        $category->description = $request->description;
        $category->visible = $request->visible;
        $category->save();


        if ($img) {
            $new_name = $img_path . '/' . $category->id . '.' . $img->getClientOriginalExtension();
            DB::table("categories")->where("id", "=", $category->id)->update([
                'img' => $new_name,
            ]);
            $img->move(public_path('/uploads/categories'), $new_name);
        } else {
            $new_name = $img_path . '/' . 'default.png';
            DB::table("categories")->where("id", "=", $category->id)->update([
                'img' => $new_name,
            ]);
        }



        $this->alert->success('You have successfully created the category.')->flash();
        return redirect(route("admin.billing.categories"));
    }

    public function categories_edit($id)
    {
        $category = DB::table("categories")->where("id", "=", $id)->get();
        return view("admin.billing.categories.edit", [
            "category" => $category
        ]);
    }

    public function categories_update($id, Request $request)
    {
        $request->validate([
            "select_file"  => "nullable|image|mimes:jpg,jpeg,png,gif|max:2048",
            "name" => "required", 
            "description" => "required", 
            "priority" => "required", 
            "visible" => "required"
        ]);

        $img_path = "/uploads/categories";
        $img = $request->file('select_file');
        if (!$img) {
            $new_name = DB::table("categories")->where("id", "=", $id)->first();
            $new_name = $new_name->img;
        }
        else {
            $new_name = $img_path . '/' . $id . '.' . $img->getClientOriginalExtension();
        }

        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->description = $request->description;
        $category->priority = $request->priority;
        $category->visible = $request->visible;
        $category->img = $new_name;
        $category->save();

        if ($img) {
            $img->move(public_path('/uploads/categories'), $new_name);
        }
        $this->alert->success('You have successfully updated the category.')->flash();
        return redirect(route("admin.billing.categories"));
    }

    public function categories_delete($id)
    {
        $category = Category::findOrFail($id);
        $image_path = public_path($category->img);
        if(file_exists($image_path)){
            File::delete($image_path);
        }
        $category->delete();
        $this->alert->success('You have successfully deleted the category.')->flash();
        return redirect(route("admin.billing.categories"));
    }

    /**
     * Products
     */

    public function products(Request $request)
    {

        $billing = DB::table("billing")->get();
        $products = QueryBuilder::for(
            Product::query()
        )
            ->allowedFilters(['name', 'description'])
            ->allowedSorts(['id'])
            ->paginate(25);

        return $this->view->make('admin.billing.products.index', [
            'products' => $products,
            'billing' => $billing
        ]);


    }
    public function products_new()
    {
        $nests = DB::select("select * from nests");
        $nodes = DB::select("select * from nodes");
        $eggs = DB::select("select * from eggs");
        $categories = DB::select("select * from categories");
        return view("admin.billing.products.new", [
            "nests" => $nests, 
            "eggs" => $eggs, 
            "nodes" => $nodes, 
            "categories" => $categories,
        ]);
    }
    public function products_store(Request $request)
    {

        $request->validate([
            "select_file"  => "nullable|image|mimes:jpg,jpeg,png,gif|max:2048",
            "name" => "required", 
            "priority" => "required", 
            "price" => "required",
            "description" => "required", 
            "visible" => "required", 
            "category" => "required",
            "egg_id" => "required",
            "node_ids" => "required",
            "memory" => "required",
            "swap" => "required",
            "cpu" => "required",
            "io" => "required",
            "disk" => "required",
            "database_limit" => "required",
            "allocation_limit" => "required",
            "backup_limit" => "required",
        ]);
        $img_path = "/uploads/products";
        $img = $request->file('select_file');


        $product = new Product;
        $product->name = $request->name;
        $product->priority = $request->priority;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->category = $request->category;
        $product->egg_id = $request->egg_id;
        $product->visible = $request->visible;
        $product->node_id = implode(',', $request->node_ids);
        $product->memory = $request->memory;
        $product->swap = $request->swap;
        $product->cpu = $request->cpu;
        $product->io = $request->io;
        $product->disk = $request->disk;
        $product->database_limit = $request->database_limit;
        $product->allocation_limit = $request->allocation_limit;
        $product->backup_limit = $request->backup_limit;
        $product->save();

        if ($img) {
            $new_name = $img_path . '/' . $product->id . '.' . $img->getClientOriginalExtension();
            DB::table("products")->where("id", "=", $product->id)->update([
                'img' => $new_name,
            ]);
            $img->move(public_path('/uploads/products'), $new_name);
        } else {
            $new_name = $img_path . '/' . 'default.png';
            DB::table("products")->where("id", "=", $product->id)->update([
                'img' => $new_name,
            ]);
        }



        $this->alert->success('You have successfully created the product.')->flash();

        return redirect(route("admin.billing.products"));
    }

    public function products_edit($id)
    {

        $products = DB::table("products")->where("id", "=", $id)->get();
        $eggs = DB::table("eggs")->get();
        $nodes = DB::table("nodes")->get();
        $categories = DB::table("categories")->get();
        if (count($products) == 0) {
            abort(404);
        } else {
            return view("admin.billing.products.edit", [
                "products" => $products, 
                "eggs" => $eggs, 
                "nodes" => $nodes, 
                "categories" => $categories
            ])->with('nests', Nest::get());
        }
    }

    public function products_update($id, Request $request)
    {

        $request->validate([
            "select_file"  => "nullable|image|mimes:jpg,jpeg,png,gif|max:2048",
            "name" => "required", 
            "priority" => "required", 
            "price" => "required",
            "description" => "required", 
            "visible" => "required", 
            "category" => "required",
            "egg_id" => "required",
            "node_ids" => "required",
            "memory" => "required",
            "swap" => "required",
            "cpu" => "required",
            "io" => "required",
            "disk" => "required",
            "database_limit" => "required",
            "allocation_limit" => "required",
            "backup_limit" => "required",
        ]);
        $img_path = "/uploads/products";
        $img = $request->file('select_file');
        if (!$img) {
            $new_name = DB::table("products")->where("id", "=", $id)->first();
            $new_name = $new_name->img;
        }
        else {
            $new_name = $img_path . '/' . $id . '.' . $img->getClientOriginalExtension();
        }

        $product = Product::findOrFail($id);
        $product->name = $request->name;
        $product->priority = $request->priority;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->category = $request->category;
        $product->egg_id = $request->egg_id;
        $product->visible = $request->visible;
        $product->node_id = implode(',', $request->node_ids);
        $product->memory = $request->memory;
        $product->swap = $request->swap;
        $product->cpu = $request->cpu;
        $product->io = $request->io;
        $product->disk = $request->disk;
        $product->database_limit = $request->database_limit;
        $product->allocation_limit = $request->allocation_limit;
        $product->backup_limit = $request->backup_limit;
        $product->img = $new_name;
        $product->save();

        if ($img) {
            $img->move(public_path('/uploads/products'), $new_name);
        }
        $this->alert->success('You have successfully updated the product.')->flash();

        return redirect(route("admin.billing.products"));
    }

    public function products_delete($id)
    {
        $product = Product::findOrFail($id);
        $image_path = public_path($category->img);
        if(file_exists($image_path)){
            File::delete($image_path);
        }
        $product->delete();
        $this->alert->success('You have successfully deleted the product.')->flash();
        return redirect(route("admin.billing.products"));
    }

    /**
     * Deploy
     */

    public function deploy()
    {
    	$nests = DB::table("nests")->get();

        return view("admin.billing.deploy.index", [
            "nests" => $nests,

        ]);
    }

    public function deploy_update(Request $request, $id)
    {

        DB::table("nests")->where("id", "=", $id)->update([
            "database_limit" => $request->database_limit, 
            "allocation_limit" => $request->allocation_limit,
            "memory_monthly_cost" => $request->memory_monthly_cost, 
            "disk_monthly_cost" => $request->disk_monthly_cost, 
            "cpu_limit" => $request->cpu_limit, 
            "max_memory" => $request->max_memory, 
            "max_disk" => $request->max_disk, 
        ]);

    	$this->alert->success('You have successfully updated the deploy settings.')->flash();
        return redirect(route("admin.billing.deploy"));
    }

    /**
     * Promotional Codes
     */

    public function promotional_codes(Request $request)
    {

        $codes = QueryBuilder::for(
            PromotionalCode::query()
        )
            ->allowedFilters(['code'])
            ->allowedSorts(['id'])
            ->paginate(25);

        return $this->view->make('admin.billing.promotional-codes.index', [
            'promotional_codes' => $codes
        ]);

    }

    public function promotional_codes_new()
    {
        return view("admin.billing.promotional-codes.new");
    }

    public function promotional_codes_store(Request $request)
    {

        $request->validate([
            "code" => "required", 
            "max_uses" => "nullable|numeric", 
            "amount" => "required|numeric|between:0,99999.99", 
            "percentage" => "required|numeric|min:0|max:100", 
            "min_amount" => "required|numeric|between:0,99999.99", 
            "max_amount" => "required|numeric|between:0,99999.99"
        ]);

        $codes = DB::table("promotional_codes")->get();
        foreach ($codes as $code) {
            if ($code->code == $request->code) {
                return redirect()->back()->withErrors("This code already exist. please use another code");
            }
        }

        if ($request->lasts_till == null || $request->lasts_till == 0) {
            DB::table("promotional_codes")->insert([
                "code" => $request->code, 
                "max_uses" => $request->max_uses,
                "amount" => $request->amount, 
                "percentage" => $request->percentage, 
                "min_basket" => $request->min_amount, 
                "max_basket" => $request->max_amount, 
                "lasts_till" => "0000-00-00 00:00:00",
                "created_at" => date("Y-m-d h:m:s"),
            ]);
        } else {
            DB::table("promotional_codes")->insert([
                "code" => $request->code, 
                "max_uses" => $request->max_uses,
                "amount" => $request->amount, 
                "percentage" => $request->percentage, 
                "min_basket" => $request->min_amount, 
                "max_basket" => $request->max_amount, 
                "lasts_till" => $request->lasts_till,
                "created_at" => date("Y-m-d h:m:s"),
            ]);
        }
        $this->alert->success('You have successfully created the promotional code.')->flash();
        return redirect(route("admin.billing.promotional-codes"));
    }

    public function promotional_codes_edit($id)
    {
        $promotional_codes = DB::table("promotional_codes")->where("id", "=", $id)->get();

        return view("admin.billing.promotional-codes.edit", [
            "promotional_codes" => $promotional_codes
        ]);
    }

    public function promotional_codes_update(Request $request, $id)
    {
        $request->validate([
            "code" => "required", 
            "max_uses" => "nullable|numeric", 
            "amount" => "required|numeric|between:0,99999.99", 
            "percentage" => "required|numeric|min:0|max:100", 
            "min_amount" => "required|numeric|between:0,99999.99", 
            "max_amount" => "required|numeric|between:0,99999.99"
        ]);

        $codes = DB::table("promotional_codes")->get();
        $pcodes = DB::table("promotional_codes")->where("id", "=", $id)->get();
        foreach ($codes as $code) {
        	foreach ($pcodes as $pcode) {
            	if ($code->code == $request->code && $pcode->code != $request->code) {
                	return redirect()->back()->withErrors("This code already exist. please use another code");
            	}
            }
        }

        if ($request->lasts_till == null || $request->lasts_till == 0) {
            DB::table("promotional_codes")->where("id", "=", $id)->update([
                "code" => $request->code, 
                "max_uses" => $request->max_uses,
                "amount" => $request->amount, 
                "percentage" => $request->percentage, 
                "min_basket" => $request->min_amount, 
                "max_basket" => $request->max_amount, 
                "lasts_till" => "0000-00-00 00:00:00",
                "updated_at" => date("Y-m-d h:m:s"),
            ]);
        } else {
            DB::table("promotional_codes")->where("id", "=", $id)->update([
                "code" => $request->code, 
                "max_uses" => $request->max_uses,
                "amount" => $request->amount, 
                "percentage" => $request->percentage, 
                "min_basket" => $request->min_amount, 
                "max_basket" => $request->max_amount, 
                "lasts_till" => $request->lasts_till,
                "updated_at" => date("Y-m-d h:m:s"),
            ]);
        }
        return redirect(route("admin.billing.promotional-codes"));
    }

    public function promotional_codes_delete($id)
    {
        $id = DB::table("promotional_codes")->where("id", "=", $id)->delete();
        
        $this->alert->success('You have successfully deleted the promotional code.')->flash();
        return redirect(route("admin.billing.promotional-codes"));
    }

    public function users(Request $request)
    {
        $users = QueryBuilder::for(
            User::query()
        )
            ->allowedFilters(['username', 'email', 'name_first', 'name_last'])
            ->allowedSorts(['id'])
            ->paginate(25);


        return $this->view->make('admin.billing.users.index', [
            'users' => $users
        ]);
    }

    public function updateuser(Request $request, $id)
    {
        $id = (int) $id;

        $balance = trim($request->input('balance'));

        $request->validate([
            "balance" => "required|numeric|min:0|max:99999"
        ]);

        if($balance >= 0) {
            DB::table('users')->where('id', '=', $id)->update([
                'balance' => $balance,
            ]);
        }

        $this->alert->success('You success fully edited this users balance.')->flash();

        return redirect()->route('admin.billing.users');
    }
}

<?php
namespace Pterodactyl\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use Pterodactyl\Exceptions\DisplayException;
use Pterodactyl\Services\Servers\ServerCreationService;
use Pterodactyl\Models\Nest;
use Pterodactyl\Models\Node;
use Illuminate\Support\Facades\DB;
use Pterodactyl\Http\Controllers\Controller;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use Mollie\Laravel\Facades\Mollie;
use Stripe\Stripe;
use Pterodactyl\Models\User;
use Pterodactyl\Models\Invoice;
use Pterodactyl\Models\Product;
use Pterodactyl\Models\Category;
use Stripe\Customer;
use Stripe\Charge;
use Validator;
use Session;

class BillingController extends ClientApiController
{
    private $creationService;
    private const COUNTRIES = array("AF" => "Afghanistan", "AX" => "Aland Islands", "AL" => "Albania", "DZ" => "Algeria", "AS" => "American Samoa", "AD" => "Andorra", "AO" => "Angola", "AI" => "Anguilla", "AQ" => "Antarctica", "AG" => "Antigua And Barbuda", "AR" => "Argentina", "AM" => "Armenia", "AW" => "Aruba", "AU" => "Australia", "AT" => "Austria", "AZ" => "Azerbaijan", "BS" => "Bahamas", "BH" => "Bahrain", "BD" => "Bangladesh", "BB" => "Barbados", "BY" => "Belarus", "BE" => "Belgium", "BZ" => "Belize", "BJ" => "Benin", "BM" => "Bermuda", "BT" => "Bhutan", "BO" => "Bolivia", "BA" => "Bosnia And Herzegovina", "BW" => "Botswana", "BV" => "Bouvet Island", "BR" => "Brazil", "IO" => "British Indian Ocean Territory", "BN" => "Brunei Darussalam", "BG" => "Bulgaria", "BF" => "Burkina Faso", "BI" => "Burundi", "KH" => "Cambodia", "CM" => "Cameroon", "CA" => "Canada", "CV" => "Cape Verde", "KY" => "Cayman Islands", "CF" => "Central African Republic", "TD" => "Chad", "CL" => "Chile", "CN" => "China", "CX" => "Christmas Island", "CC" => "Cocos (Keeling) Islands", "CO" => "Colombia", "KM" => "Comoros", "CG" => "Congo", "CD" => "Congo, Democratic Republic", "CK" => "Cook Islands", "CR" => "Costa Rica", "CI" => "Cote D\47Ivoire", "HR" => "Croatia", "CU" => "Cuba", "CY" => "Cyprus", "CZ" => "Czech Republic", "DK" => "Denmark", "DJ" => "Djibouti", "DM" => "Dominica", "DO" => "Dominican Republic", "EC" => "Ecuador", "EG" => "Egypt", "SV" => "El Salvador", "GQ" => "Equatorial Guinea", "ER" => "Eritrea", "EE" => "Estonia", "ET" => "Ethiopia", "FK" => "Falkland Islands (Malvinas)", "FO" => "Faroe Islands", "FJ" => "Fiji", "FI" => "Finland", "FR" => "France", "GF" => "French Guiana", "PF" => "French Polynesia", "TF" => "French Southern Territories", "GA" => "Gabon", "GM" => "Gambia", "GE" => "Georgia", "DE" => "Germany", "GH" => "Ghana", "GI" => "Gibraltar", "GR" => "Greece", "GL" => "Greenland", "GD" => "Grenada", "GP" => "Guadeloupe", "GU" => "Guam", "GT" => "Guatemala", "GG" => "Guernsey", "GN" => "Guinea", "GW" => "Guinea-Bissau", "GY" => "Guyana", "HT" => "Haiti", "HM" => "Heard Island & Mcdonald Islands", "VA" => "Holy See (Vatican City State)", "HN" => "Honduras", "HK" => "Hong Kong", "HU" => "Hungary", "IS" => "Iceland", "IN" => "India", "ID" => "Indonesia", "IR" => "Iran, Islamic Republic Of", "IQ" => "Iraq", "IE" => "Ireland", "IM" => "Isle Of Man", "IL" => "Israel", "IT" => "Italy", "JM" => "Jamaica", "JP" => "Japan", "JE" => "Jersey", "JO" => "Jordan", "KZ" => "Kazakhstan", "KE" => "Kenya", "KI" => "Kiribati", "KR" => "Korea", "KW" => "Kuwait", "KG" => "Kyrgyzstan", "LA" => "Lao People\47s Democratic Republic", "LV" => "Latvia", "LB" => "Lebanon", "LS" => "Lesotho", "LR" => "Liberia", "LY" => "Libyan Arab Jamahiriya", "LI" => "Liechtenstein", "LT" => "Lithuania", "LU" => "Luxembourg", "MO" => "Macao", "MK" => "Macedonia", "MG" => "Madagascar", "MW" => "Malawi", "MY" => "Malaysia", "MV" => "Maldives", "ML" => "Mali", "MT" => "Malta", "MH" => "Marshall Islands", "MQ" => "Martinique", "MR" => "Mauritania", "MU" => "Mauritius", "YT" => "Mayotte", "MX" => "Mexico", "FM" => "Micronesia, Federated States Of", "MD" => "Moldova", "MC" => "Monaco", "MN" => "Mongolia", "ME" => "Montenegro", "MS" => "Montserrat", "MA" => "Morocco", "MZ" => "Mozambique", "MM" => "Myanmar", "NA" => "Namibia", "NR" => "Nauru", "NP" => "Nepal", "NL" => "Netherlands", "AN" => "Netherlands Antilles", "NC" => "New Caledonia", "NZ" => "New Zealand", "NI" => "Nicaragua", "NE" => "Niger", "NG" => "Nigeria", "NU" => "Niue", "NF" => "Norfolk Island", "MP" => "Northern Mariana Islands", "NO" => "Norway", "OM" => "Oman", "PK" => "Pakistan", "PW" => "Palau", "PS" => "Palestinian Territory, Occupied", "PA" => "Panama", "PG" => "Papua New Guinea", "PY" => "Paraguay", "PE" => "Peru", "PH" => "Philippines", "PN" => "Pitcairn", "PL" => "Poland", "PT" => "Portugal", "PR" => "Puerto Rico", "QA" => "Qatar", "RE" => "Reunion", "RO" => "Romania", "RU" => "Russian Federation", "RW" => "Rwanda", "BL" => "Saint Barthelemy", "SH" => "Saint Helena", "KN" => "Saint Kitts And Nevis", "LC" => "Saint Lucia", "MF" => "Saint Martin", "PM" => "Saint Pierre And Miquelon", "VC" => "Saint Vincent And Grenadines", "WS" => "Samoa", "SM" => "San Marino", "ST" => "Sao Tome And Principe", "SA" => "Saudi Arabia", "SN" => "Senegal", "RS" => "Serbia", "SC" => "Seychelles", "SL" => "Sierra Leone", "SG" => "Singapore", "SK" => "Slovakia", "SI" => "Slovenia", "SB" => "Solomon Islands", "SO" => "Somalia", "ZA" => "South Africa", "GS" => "South Georgia And Sandwich Isl.", "ES" => "Spain", "LK" => "Sri Lanka", "SD" => "Sudan", "SR" => "Suriname", "SJ" => "Svalbard And Jan Mayen", "SZ" => "Swaziland", "SE" => "Sweden", "CH" => "Switzerland", "SY" => "Syrian Arab Republic", "TW" => "Taiwan", "TJ" => "Tajikistan", "TZ" => "Tanzania", "TH" => "Thailand", "TL" => "Timor-Leste", "TG" => "Togo", "TK" => "Tokelau", "TO" => "Tonga", "TT" => "Trinidad And Tobago", "TN" => "Tunisia", "TR" => "Turkey", "TM" => "Turkmenistan", "TC" => "Turks And Caicos Islands", "TV" => "Tuvalu", "UG" => "Uganda", "UA" => "Ukraine", "AE" => "United Arab Emirates", "GB" => "United Kingdom", "US" => "United States", "UM" => "United States Outlying Islands", "UY" => "Uruguay", "UZ" => "Uzbekistan", "VU" => "Vanuatu", "VE" => "Venezuela", "VN" => "Viet Nam", "VG" => "Virgin Islands, British", "VI" => "Virgin Islands, U.S.", "WF" => "Wallis And Futuna", "EH" => "Western Sahara", "YE" => "Yemen", "ZM" => "Zambia", "ZW" => "Zimbabwe");

    public function __construct(ServerCreationService $creationservice)
    {
        $this->creationService = $creationservice;
    }
    public function index(Request $request): array
    {   
        $user = DB::table("users")->where('id', '=', $request->user()->id)->get();
        $billing = DB::table("billing")->get();
        $gateways = DB::table("gateways")->get();

    	$countries = '[{"code":"AF","country":"Afghanistan"},{"code":"AX","country":"Aland Islands"},{"code":"AL","country":"Albania"},{"code":"DZ","country":"Algeria"},{"code":"AS","country":"American Samoa"},{"code":"AD","country":"Andorra"},{"code":"AO","country":"Angola"},{"code":"AI","country":"Anguilla"},{"code":"AQ","country":"Antarctica"},{"code":"AG","country":"Antigua And Barbuda"},{"code":"AR","country":"Argentina"},{"code":"AM","country":"Armenia"},{"code":"AW","country":"Aruba"},{"code":"AU","country":"Australia"},{"code":"AT","country":"Austria"},{"code":"AZ","country":"Azerbaijan"},{"code":"BS","country":"Bahamas"},{"code":"BH","country":"Bahrain"},{"code":"BD","country":"Bangladesh"},{"code":"BB","country":"Barbados"},{"code":"BY","country":"Belarus"},{"code":"BE","country":"Belgium"},{"code":"BZ","country":"Belize"},{"code":"BJ","country":"Benin"},{"code":"BM","country":"Bermuda"},{"code":"BT","country":"Bhutan"},{"code":"BO","country":"Bolivia"},{"code":"BA","country":"Bosnia And Herzegovina"},{"code":"BW","country":"Botswana"},{"code":"BV","country":"Bouvet Island"},{"code":"BR","country":"Brazil"},{"code":"IO","country":"British Indian Ocean Territory"},{"code":"BN","country":"Brunei Darussalam"},{"code":"BG","country":"Bulgaria"},{"code":"BF","country":"Burkina Faso"},{"code":"BI","country":"Burundi"},{"code":"KH","country":"Cambodia"},{"code":"CM","country":"Cameroon"},{"code":"CA","country":"Canada"},{"code":"CV","country":"Cape Verde"},{"code":"KY","country":"Cayman Islands"},{"code":"CF","country":"Central African Republic"},{"code":"TD","country":"Chad"},{"code":"CL","country":"Chile"},{"code":"CN","country":"China"},{"code":"CX","country":"Christmas Island"},{"code":"CC","country":"Cocos (Keeling) Islands"},{"code":"CO","country":"Colombia"},{"code":"KM","country":"Comoros"},{"code":"CG","country":"Congo"},{"code":"CD","country":"Congo, Democratic Republic"},{"code":"CK","country":"Cook Islands"},{"code":"CR","country":"Costa Rica"},{"code":"CI","country":"Cote D\'Ivoire"},{"code":"HR","country":"Croatia"},{"code":"CU","country":"Cuba"},{"code":"CY","country":"Cyprus"},{"code":"CZ","country":"Czech Republic"},{"code":"DK","country":"Denmark"},{"code":"DJ","country":"Djibouti"},{"code":"DM","country":"Dominica"},{"code":"DO","country":"Dominican Republic"},{"code":"EC","country":"Ecuador"},{"code":"EG","country":"Egypt"},{"code":"SV","country":"El Salvador"},{"code":"GQ","country":"Equatorial Guinea"},{"code":"ER","country":"Eritrea"},{"code":"EE","country":"Estonia"},{"code":"ET","country":"Ethiopia"},{"code":"FK","country":"Falkland Islands (Malvinas)"},{"code":"FO","country":"Faroe Islands"},{"code":"FJ","country":"Fiji"},{"code":"FI","country":"Finland"},{"code":"FR","country":"France"},{"code":"GF","country":"French Guiana"},{"code":"PF","country":"French Polynesia"},{"code":"TF","country":"French Southern Territories"},{"code":"GA","country":"Gabon"},{"code":"GM","country":"Gambia"},{"code":"GE","country":"Georgia"},{"code":"DE","country":"Germany"},{"code":"GH","country":"Ghana"},{"code":"GI","country":"Gibraltar"},{"code":"GR","country":"Greece"},{"code":"GL","country":"Greenland"},{"code":"GD","country":"Grenada"},{"code":"GP","country":"Guadeloupe"},{"code":"GU","country":"Guam"},{"code":"GT","country":"Guatemala"},{"code":"GG","country":"Guernsey"},{"code":"GN","country":"Guinea"},{"code":"GW","country":"Guinea-Bissau"},{"code":"GY","country":"Guyana"},{"code":"HT","country":"Haiti"},{"code":"HM","country":"Heard Island & Mcdonald Islands"},{"code":"VA","country":"Holy See (Vatican City State)"},{"code":"HN","country":"Honduras"},{"code":"HK","country":"Hong Kong"},{"code":"HU","country":"Hungary"},{"code":"IS","country":"Iceland"},{"code":"IN","country":"India"},{"code":"ID","country":"Indonesia"},{"code":"IR","country":"Iran, Islamic Republic Of"},{"code":"IQ","country":"Iraq"},{"code":"IE","country":"Ireland"},{"code":"IM","country":"Isle Of Man"},{"code":"IL","country":"Israel"},{"code":"IT","country":"Italy"},{"code":"JM","country":"Jamaica"},{"code":"JP","country":"Japan"},{"code":"JE","country":"Jersey"},{"code":"JO","country":"Jordan"},{"code":"KZ","country":"Kazakhstan"},{"code":"KE","country":"Kenya"},{"code":"KI","country":"Kiribati"},{"code":"KR","country":"Korea"},{"code":"KW","country":"Kuwait"},{"code":"KG","country":"Kyrgyzstan"},{"code":"LA","country":"Lao People\'s Democratic Republic"},{"code":"LV","country":"Latvia"},{"code":"LB","country":"Lebanon"},{"code":"LS","country":"Lesotho"},{"code":"LR","country":"Liberia"},{"code":"LY","country":"Libyan Arab Jamahiriya"},{"code":"LI","country":"Liechtenstein"},{"code":"LT","country":"Lithuania"},{"code":"LU","country":"Luxembourg"},{"code":"MO","country":"Macao"},{"code":"MK","country":"Macedonia"},{"code":"MG","country":"Madagascar"},{"code":"MW","country":"Malawi"},{"code":"MY","country":"Malaysia"},{"code":"MV","country":"Maldives"},{"code":"ML","country":"Mali"},{"code":"MT","country":"Malta"},{"code":"MH","country":"Marshall Islands"},{"code":"MQ","country":"Martinique"},{"code":"MR","country":"Mauritania"},{"code":"MU","country":"Mauritius"},{"code":"YT","country":"Mayotte"},{"code":"MX","country":"Mexico"},{"code":"FM","country":"Micronesia, Federated States Of"},{"code":"MD","country":"Moldova"},{"code":"MC","country":"Monaco"},{"code":"MN","country":"Mongolia"},{"code":"ME","country":"Montenegro"},{"code":"MS","country":"Montserrat"},{"code":"MA","country":"Morocco"},{"code":"MZ","country":"Mozambique"},{"code":"MM","country":"Myanmar"},{"code":"NA","country":"Namibia"},{"code":"NR","country":"Nauru"},{"code":"NP","country":"Nepal"},{"code":"NL","country":"Netherlands"},{"code":"AN","country":"Netherlands Antilles"},{"code":"NC","country":"New Caledonia"},{"code":"NZ","country":"New Zealand"},{"code":"NI","country":"Nicaragua"},{"code":"NE","country":"Niger"},{"code":"NG","country":"Nigeria"},{"code":"NU","country":"Niue"},{"code":"NF","country":"Norfolk Island"},{"code":"MP","country":"Northern Mariana Islands"},{"code":"NO","country":"Norway"},{"code":"OM","country":"Oman"},{"code":"PK","country":"Pakistan"},{"code":"PW","country":"Palau"},{"code":"PS","country":"Palestinian Territory, Occupied"},{"code":"PA","country":"Panama"},{"code":"PG","country":"Papua New Guinea"},{"code":"PY","country":"Paraguay"},{"code":"PE","country":"Peru"},{"code":"PH","country":"Philippines"},{"code":"PN","country":"Pitcairn"},{"code":"PL","country":"Poland"},{"code":"PT","country":"Portugal"},{"code":"PR","country":"Puerto Rico"},{"code":"QA","country":"Qatar"},{"code":"RE","country":"Reunion"},{"code":"RO","country":"Romania"},{"code":"RU","country":"Russian Federation"},{"code":"RW","country":"Rwanda"},{"code":"BL","country":"Saint Barthelemy"},{"code":"SH","country":"Saint Helena"},{"code":"KN","country":"Saint Kitts And Nevis"},{"code":"LC","country":"Saint Lucia"},{"code":"MF","country":"Saint Martin"},{"code":"PM","country":"Saint Pierre And Miquelon"},{"code":"VC","country":"Saint Vincent And Grenadines"},{"code":"WS","country":"Samoa"},{"code":"SM","country":"San Marino"},{"code":"ST","country":"Sao Tome And Principe"},{"code":"SA","country":"Saudi Arabia"},{"code":"SN","country":"Senegal"},{"code":"RS","country":"Serbia"},{"code":"SC","country":"Seychelles"},{"code":"SL","country":"Sierra Leone"},{"code":"SG","country":"Singapore"},{"code":"SK","country":"Slovakia"},{"code":"SI","country":"Slovenia"},{"code":"SB","country":"Solomon Islands"},{"code":"SO","country":"Somalia"},{"code":"ZA","country":"South Africa"},{"code":"GS","country":"South Georgia And Sandwich Isl."},{"code":"ES","country":"Spain"},{"code":"LK","country":"Sri Lanka"},{"code":"SD","country":"Sudan"},{"code":"SR","country":"Suriname"},{"code":"SJ","country":"Svalbard And Jan Mayen"},{"code":"SZ","country":"Swaziland"},{"code":"SE","country":"Sweden"},{"code":"CH","country":"Switzerland"},{"code":"SY","country":"Syrian Arab Republic"},{"code":"TW","country":"Taiwan"},{"code":"TJ","country":"Tajikistan"},{"code":"TZ","country":"Tanzania"},{"code":"TH","country":"Thailand"},{"code":"TL","country":"Timor-Leste"},{"code":"TG","country":"Togo"},{"code":"TK","country":"Tokelau"},{"code":"TO","country":"Tonga"},{"code":"TT","country":"Trinidad And Tobago"},{"code":"TN","country":"Tunisia"},{"code":"TR","country":"Turkey"},{"code":"TM","country":"Turkmenistan"},{"code":"TC","country":"Turks And Caicos Islands"},{"code":"TV","country":"Tuvalu"},{"code":"UG","country":"Uganda"},{"code":"UA","country":"Ukraine"},{"code":"AE","country":"United Arab Emirates"},{"code":"GB","country":"United Kingdom"},{"code":"US","country":"United States"},{"code":"UM","country":"United States Outlying Islands"},{"code":"UY","country":"Uruguay"},{"code":"UZ","country":"Uzbekistan"},{"code":"VU","country":"Vanuatu"},{"code":"VE","country":"Venezuela"},{"code":"VN","country":"Viet Nam"},{"code":"VG","country":"Virgin Islands, British"},{"code":"VI","country":"Virgin Islands, U.S."},{"code":"WF","country":"Wallis And Futuna"},{"code":"EH","country":"Western Sahara"},{"code":"YE","country":"Yemen"},{"code":"ZM","country":"Zambia"},{"code":"ZW","country":"Zimbabwe"}]';
    	$countries = json_decode($countries);


        foreach ($billing as $key => $settings) {
            $billing[$key]->code = '&'.$settings->currency.';'; 
        }


        return [
            'success' => true,
            'data' => [
                'user' => $user,
                'billing' => $billing,
                'gateways' => $gateways,
                'countries' => $countries,
            ],
        ];

    }

    public function update(Request $request): array
    {
        /*$request->validate([
            'first_name' => 'required|string|min:3|max:255',
            'last_name' => 'required|string|min:3|max:255',
            'address' => 'required|string|min:3|max:255',
            'city' => 'required|string|min:3|max:255',
            'country' => 'required|string|max:2|in:'.implode(',', array_keys(self::COUNTRIES)),
            'zip' => 'required|string|min:3|max:6',
        ]);*/


        DB::table('users')->where("id", $request->user()->id)->update([
            'billing_first_name' => $request->input('first_name'),
            'billing_last_name' => $request->input('last_name'),
            'billing_address' => $request->input('address'),
            'billing_city' => $request->input('city'),
            'billing_country' => $request->input('country'),
            'billing_zip' => $request->input('zip'),
        ]);


        return [
            'success' => true,
            'data' => [],
        ];
    }

    public function store(Request $request): array
    {
        $billing = DB::table("billing")->get();

        foreach ($billing as $key => $settings) {
            $billing[$key]->code = '&'.$settings->currency.';'; 
        }

        $categories = DB::table("categories")->orderBy("priority", "asc")->get();
        $categories_settings = DB::table("billing")
        ->select('categories_img','categories_img_rounded','categories_img_width','categories_img_height')
        ->get();

        $products = DB::table("products")->orderBy("priority", "asc")->get();
        $products_settings = DB::table("billing")
        ->select('products_img','products_img_rounded','products_img_width','products_img_height')
        ->get();
        
        return [
            'success' => true,
            'data' => [
                'billing' => $billing,
                'categories' => $categories,
                'categories_settings' => $categories_settings,
                'products' => $products,
                'products_settings' => $products_settings,
            ],
        ];

    }

    public function categories(Request $request): array
    {
        $categories = DB::table("categories")->orderBy("priority", "asc")->get();
        $settings = DB::table("billing")->select('categories_img','categories_img_rounded','categories_img_width','categories_img_height')->get();
        
        $cart = DB::table("carts")->where('user_id', '=', $request->user()->id)->get();
        return [
            'success' => true,
            'data' => [
                'categories' => $categories,
                'settings' => $settings,
                'cart' => $cart,
            ],
        ];

    }

    public function category(Request $request, $id): array
    {
        $products = DB::table("products")->where("category", "=", $id)->orderBy("priority", "asc")->get();
        $settings = DB::table("billing")->select('products_img','products_img_rounded','products_img_width','products_img_height','currency')->get();

        foreach ($products as $key => $product) {
            $products[$key]->code = '&'.$settings[0]->currency.';'; 
        }

        $cart = DB::table("carts")->where('user_id', '=', $request->user()->id)->get();

        return [
            'success' => true,
            'data' => [
                'products' => $products,
                'settings' => $settings,
                'cart' => $cart,
            ],
        ];
    }

    public function products(Request $request): array
    {
        $products = DB::table("products")->orderBy("priority", "asc")->get();
        $settings = DB::table("billing")->select('products_img','products_img_rounded','products_img_width','products_img_height','currency')->get();

        foreach ($products as $key => $product) {
            $products[$key]->code = '&'.$settings[0]->currency.';'; 
        }

        $cart = DB::table("carts")->where('user_id', '=', $request->user()->id)->get();

        return [
            'success' => true,
            'data' => [
                'products' => $products,
                'settings' => $settings,
                'cart' => $cart,
            ],
        ];

    }

    public function invoices(Request $request): array
    { 
        $billing = DB::table("billing")->get();
        $invoices = DB::table("invoices")->where('user_id', '=', $request->user()->id)->orderBy("id", "desc")->get();

        foreach ($invoices as $key => $invoice) {
            $invoices[$key]->currency = '&'.$billing[0]->currency.';'; 
        }

        return [
            'success' => true,
            'data' => [
                'billing' => $billing,
                'invoices' => $invoices,
            ],
        ];
    }

    public function invoice_pdf(Request $request)
    {
        $invoice = $request->user()->invoices()->find($request->id);

        if (!$invoice) return abort(404);
        return $invoice->downloadPdf();
    }


    public function addBalance($amount) 
    {
        $invoice = new Invoice();
        $invoice->amount = $amount;
        $invoice->user_id = $this->id;
        $invoice->billing_first_name = $this->billing_first_name;
        $invoice->billing_last_name = $this->billing_last_name;
        $invoice->billing_address = $this->billing_address;
        $invoice->billing_city = $this->billing_city;
        $invoice->billing_country = $this->billing_country;
        $invoice->billing_zip = $this->billing_zip;
        $invoice->save();
        $this->balance += $invoice->amount;
        $this->save();
    }

    public function dedipass(Request $request)
    {
        return view("base.billing")
        ->with("user", $request->user())
        ->with("countries", self::COUNTRIES)
        ->with("invoices", $request->user()->invoices()->orderBy("id", "desc")->paginate(5))
        ->with("billing", DB::table("billing")->get());
    }

    public function dedipassCallback(Request $request)
    {
        $gateway_dedipass = DB::table("gateways")->where("gateway", "=", "dedipass")->get();
        foreach ($gateway_dedipass as $gateway) {
            $public_key = $gateway->api;
            $private_key = $gateway->private_key;
        }

        $code = $request->input('code') ? preg_replace('/[^a-zA-Z0-9]+/', '', $request->input('code')) : '';
        $dedipass = file_get_contents('http://api.dedipass.com/v1/pay/?public_key=' . $public_key . '&private_key=' . $private_key . '&code=' . $code);
        $dedipass = json_decode($dedipass);

        if($dedipass->status == 'success') {

            $user = User::where("id", "=", auth()->user()->id)->first();
            $balance = $user->balance + $dedipass->virtual_currency;

            User::where("id", "=", $request->user()->id)->update([
                "balance" => $balance
            ]);

            $invoice = new Invoice();
            $invoice->amount = $dedipass->virtual_currency;
            $invoice->reason = "Top up Credit";
            $invoice->user_id = $user->id;
            $invoice->billing_first_name = $user->billing_first_name;
            $invoice->billing_last_name = $user->billing_last_name;
            $invoice->billing_address = $user->billing_address;
            $invoice->billing_city = $user->billing_city;
            $invoice->billing_country = $user->billing_country;
            $invoice->billing_zip = $user->billing_zip;
            $invoice->save();

            $this->alert->success($dedipass->message)->flash();
            return redirect()->back();

        } else {

            $this->alert->danger($dedipass->message)->flash();
            return redirect()->back();
        }
        
    }



    public function payssionCallback(Request $request)
    {

    }   

    private function validateBilling($user)
    {
        if (!$user->billing_first_name) return false;
        if (!$user->billing_last_name) return false;
        if (!$user->billing_address) return false;
        if (!$user->billing_city) return false;
        if (!$user->billing_country) return false;
        if (!$user->billing_zip) return false;
        return true;
    }
    public function link(Request $request)
    {
        $request->validate([
            "amount" => "required|numeric|min:5|max:1000", 
            "card_token" => "required"
        ]);

        $gateway_stripe = DB::table("gateways")->where("gateway", "=", "stripe")->get();

        foreach ($gateway_stripe as $gateway) {
            $STRIPE_SECRET_KEY = $gateway->private_key;
        }


        Stripe::setApiKey($STRIPE_SECRET_KEY);

        $user = $request->user();

        if (!$this->validateBilling($user)) {

            return redirect()->back()->withErrors("You need to fill up your billing info before making any payments.");
        }

        try {

            $customer = Customer::create([
                'email' => $user->email,
                'source'  => $request->card_token
            ]);

            $billing = DB::table("billing")->get();
            $currency_code = $billing->currency_code;

            $charge = Charge::create([
                'customer' => $customer->id,
                'amount'   => $request->amount * 100,
                'currency' => strtolower($currency_code)
            ]);

            if ($charge->paid) {

                $user->stripe_card_brand = $request->card_brand;
                $user->stripe_card_last4 = $request->card_last4;
                $user->stripe_customer_id = $customer->id;
                $user->addBalance($request->amount);

            } else {

                return redirect()->back()->withErrors("You need to fill up your billing info before making any payments.");

            }

        } catch (\Exception $ex) {}

        return redirect()->back();
    }

    public function unlink(Request $request)
    {
        $user = $request->user();
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        try {
            Customer::retrieve($user->stripe_customer_id)->delete();
        } catch (\Exception $ex) {}
        $user->stripe_customer_id = null;
        $user->stripe_card_brand = null;
        $user->stripe_card_last4 = null;
        $user->save();
        return redirect()->back();
    }

    private function getPaypalApiContext()
    {   

        $gateway_paypal = DB::table("gateways")->where("gateway", "=", "paypal")->get();

        if (count($gateway_paypal) == 0) {
            throw new DisplayException('payment account not configured correclty please contact an administator');
        } else {

            foreach ($gateway_paypal as $gateway) {
                $PAYPAL_CLIENT_ID = $gateway->api;
                $PAYPAL_CLIENT_SECRET = $gateway->private_key;
                $PAYPAL_MODE = $gateway->mode;
            }


            $apiContext = new ApiContext(new OAuthTokenCredential($PAYPAL_CLIENT_ID, $PAYPAL_CLIENT_SECRET, $PAYPAL_MODE));
            //$apiContext = new ApiContext(new OAuthTokenCredential(env("PAYPAL_CLIENT_ID"), env("PAYPAL_CLIENT_SECRET"), env("PAYPAL_CLIENT_ENV")));
            if ($PAYPAL_MODE == "live") {
                $apiContext->setConfig(array(
                    "mode" => "live", 
                    "log.LogEnabled" => true, 
                    "log.FileName" => "PayPal.log", 
                    "log.LogLevel" => 
                    "FINE"
                ));
            }
            return $apiContext;
        }  
        
    }
    public function paypal(Request $request): array
    {
    	$amount = 10;
        $balance = $request->user()->balance + $amount;

        User::where("id", "=", $request->user()->id)->update([
            "balance" => $balance
        ]);

            $invoice = new Invoice();
            $invoice->amount = $amount;
            $invoice->reason = "Top up Credit";
            $invoice->user_id = $request->user()->id;
            $invoice->billing_first_name = $request->user()->billing_first_name;
            $invoice->billing_last_name = $request->user()->billing_last_name;
            $invoice->billing_address = $request->user()->billing_address;
            $invoice->billing_city = $request->user()->billing_city;
            $invoice->billing_country = $request->user()->billing_country;
            $invoice->billing_zip = $request->user()->billing_zip;
            $invoice->save();

        return [
            'success' => true,
            'data' => [],
        ];


        /*$request->validate([
            "amount" => "required|numeric|min:2|max:1000"
        ]);

        if (!$this->validateBilling($request->user())) {
            throw new DisplayException('You need to fill up your billing info before making any payments.');
        }

        /*$billing = DB::table("billing")->get();

        foreach ($billing as $Cbd925bf51205fd1) {
            $apiContext = $this->getPaypalApiContext();
            $payer = new Payer();
            $payer->setPaymentMethod("paypal");
            $amount = new Amount();
            $amount->setTotal($request->amount);
            if ($Cbd925bf51205fd1->currency == "euro") {
                $amount->setCurrency("EUR");
            }
            if ($Cbd925bf51205fd1->currency == "dollar") {
                $amount->setCurrency("USD");
            }
            if ($Cbd925bf51205fd1->currency == "pound") {
                $amount->setCurrency("GBP");
            }
            $transaction = new Transaction();
            $transaction->setamount($amount);
            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl(route("billing.paypal.callback"));
            $redirectUrls->setCancelUrl(route("billing.paypal.callback"));
            $payment = new Payment();
            $payment->setIntent("sale");
            $payment->setPayer($payer);
            $payment->setTransactions(array($transaction));
            $payment->setRedirectUrls($redirectUrls);
            try {
                $payment->create($apiContext);
                $links = array_filter($payment->links, function ($link) {
                    return $link->rel == "approval_url";
                });
                $link = reset($links)->getHref();
                $meta[$payment->id] = $request->amount;
                session()->put("paypal_meta", $meta);
                return redirect($link);
            } catch (\Exception $ex) {}
            throw new DisplayException("Something went wrong with getting the Paypal Link, try again." . $ex);
        }*/

    }

    public function paypalCallback(Request $request): array
    {
        $apiContext = $this->getPaypalApiContext();
        if (!$request->has("paymentId") || !session()->has("paypal_meta.{$request->paymentId}")) {
            throw new DisplayException("Something went wrong during the paypal transaction!");
        }
        $user = $request->user();
        $amount = $request->session()->pull("paypal_meta.{$request->paymentId}");
        $apiContext = $this->getPaypalApiContext();
        $payment = Payment::get($request->paymentId, $apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId($request->PayerID);
        $result = $payment->execute($execution, $apiContext);
        if ($result->getState() == "approved") {

            $balance = $request->user()->balance + $amount;

            User::where("id", "=", $request->user()->id)->update([
                "balance" => $balance
            ]);

            $invoice = new Invoice();
            $invoice->amount = $amount;
            $invoice->reason = "Top up Credit";
            $invoice->user_id = $request->user()->id;
            $invoice->billing_first_name = $request->user()->billing_first_name;
            $invoice->billing_last_name = $request->user()->billing_last_name;
            $invoice->billing_address = $request->user()->billing_address;
            $invoice->billing_city = $request->user()->billing_city;
            $invoice->billing_country = $request->user()->billing_country;
            $invoice->billing_zip = $request->user()->billing_zip;
            $invoice->save();

       }

        return [
            'success' => true,
            'data' => [],
        ];
    }


    public function checkout(Request $request): array
    {
        $categories = DB::select("select * from categories");
        $billing = DB::table("billing")->get();

        $cart = DB::table("carts")->where('user_id', '=', $request->user()->id)->get();

        $total_price = 0;
        $balance = 0;
        foreach ($cart as $key => $value) {
        	$cart[$key]->product = DB::table("products")->select('name','memory','disk','price')->where('id', '=', $value->product_id)->first();
            $cart[$key]->price = $cart[$key]->product->price * $value->quantity;

            $product = DB::table("products")->where('id', '=', $value->product_id)->first();

            $total_price = $total_price + $cart[$key]->price;

            $balance = DB::table("users")->where('id', '=', $request->user()->id)->first();
            $balance = $balance->balance;

            $cart[$key]->code = '&'.$billing[0]->currency.';';
        }

        foreach ($billing as $key => $value) {
            $billing[$key]->code = '&'.$billing[0]->currency.';';
        }

        return [
            'success' => true,
            'data' => [
            	"cart" => $cart,
                "total_price" => $total_price,
                "balance" => $balance,
                "billing" => $billing
            ],
        ];
    }

    public function add_product(Request $request, $product_id): array
    {	

        $product = (int) $product_id;
        $cart = DB::table("carts")->where('user_id', '=', $request->user()->id)->where('product_id', '=', $product)->get();

        if (count($cart) < 1) {
            DB::table("carts")->insert([
                "product_id" => $product,
                "quantity" => 1,
                "user_id" => $request->user()->id
            ]);
        } else {
            DB::table("carts")->where('user_id', '=', $request->user()->id)->where('product_id', '=', $product)->update([
                "quantity" => $cart[0]->quantity + 1
            ]);
        }

        return [
            'success' => true,
            'data' => [],
        ];

    }

    public function update_product(Request $request, $product_id): array
    {
        $product = (int) $product_id;

        if($request->id and $request->quantity)
        {
            $cart = Session::get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            Session::put('cart', $cart);
        }

        return [
            'success' => true,
            'data' => [],
        ];

    }
    public function remove_product(Request $request, $product_id): array
    {

        $product = (int) $product_id;
        $cart = DB::table("carts")->where('user_id', '=', $request->user()->id)->where('product_id', '=', $product)->get();

        if (count($cart) < 1) {
            throw new DisplayException('Request not found.');
        }

        DB::table("carts")->where('user_id', '=', $request->user()->id)->where('product_id', '=', $product)->delete();

        return [
            'success' => true,
            'data' => [],
        ];
    }

    public function empty_cart(Request $request): array
    {
        DB::table("carts")->where('user_id', '=', $request->user()->id)->delete();

        return [
            'success' => true,
            'data' => [],
        ];
    }

    public function deploy(Request $request)
    {
        return view('base.billing.deploy')->with('nests', Nest::get());
    }

    public function deploy_store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'nest' => 'required|exists:nests,id',
            'egg' => 'required|exists:eggs,id',
        ]);
        $nest = Nest::find($request->nest);
        $egg = $nest->eggs()->find($request->egg);
        if (!$nest || !$egg) return redirect()->back();
        $request->validate([
            'ram' => 'required|numeric|min:256|max:'.$nest->max_memory,
            'disk' => 'required|numeric|min:256|max:'.$nest->max_disk,
        ]);
        $allocation = $this->getAllocationId($request->ram);
        $cost = ($request->ram / 1024) * $nest->memory_monthly_cost;
        $cost += ($request->disk / 1024) * $nest->disk_monthly_cost;
        if (!$allocation) return redirect()->back()->withErrors(trans('base.errors.deploy.full'));
        if ($request->user()->balance < $cost) return redirect()->back()->withErrors(trans('base.errors.deploy.founds', ['cost' => $cost]));
        $data = [
            'name' => $request->name,
            'owner_id' => $request->user()->id,
            'egg_id' => $egg->id,
            'nest_id' => $nest->id,
            'allocation_id' => $allocation,
            'environment' => [],
            'memory' => $request->ram,
            'disk' => $request->disk,
            'cpu' => $nest->cpu_limit,
            'swap' => 0,
            'io' => 500,
            'database_limit' => $nest->database_limit,
            'allocation_limit' => $nest->allocations_limit,
            'image' => $egg->docker_image,
            'startup' => $egg->startup,
            'start_on_completion' => true,
        ];
        foreach ($egg->variables()->where('user_editable', 1)->get() as $var) {
            $key = "v{$nest->id}-{$egg->id}-{$var->env_variable}";
            $data['environment'][$var->env_variable] = $request->get($key, $var->default);
            $request->validate([
                $key => $var->rules
            ]);
        }
        $server = $this->creationService->handle($data);
        $server->monthly_cost = $cost;
        $server->save();

        $invoice = new Invoice;
        $invoice->amount = $cost;
        $invoice->reason = $request->name;
        $invoice->user_id = $request->user()->id;
        $invoice->billing_first_name = $request->user()->billing_first_name;
        $invoice->billing_last_name = $request->user()->billing_last_name;
        $invoice->billing_address = $request->user()->billing_address;
        $invoice->billing_city = $request->user()->billing_city;
        $invoice->billing_country = $request->user()->billing_country;
        $invoice->billing_zip = $request->user()->billing_zip;
        $invoice->save();


        /*DB::table("invoices")->insert([
            "amount" => $cost, 
            "reason" => $request->name, 
            "user_id" => $request->user()->id, 
            "billing_first_name" => $request->user()->billing_first_name, 
            "billing_last_name" => $request->user()->billing_last_name, 
            "billing_address" => $request->user()->billing_address, 
            "billing_city" => $request->user()->billing_city, 
            "billing_country" => $request->user()->billing_country, 
            "billing_zip" => $request->user()->billing_zip
        ]);*/

        // Notifications
        /*$users = User::where('id', '=', $request->user()->id)->get();
        foreach ($users as $user) {
            $user->notify(new NewOrder($invoice));

        }*/

        return redirect()->route('index');
    }

    public function buy(Request $request): array
    {
        if (!$this->validateBilling($request->user())) {
            throw new DisplayException("You need to fill up your billing info before making any payments.");
        }

        $carts = DB::table("carts")->where('user_id', '=', $request->user()->id)->get();

        if (!$carts) {
            //abort(404);
        } else {
        	$total_price = 0;
        	foreach ($carts as $key => $value) {

        		$carts[$key]->product = DB::table("products")
        		->select('name','memory','disk','price')
        		->where('id', '=', $value->product_id)
        		->first();

            	$carts[$key]->price = $carts[$key]->product->price * $value->quantity;

            	$total_price = $total_price + $carts[$key]->price;
        	}
            if (auth()->user()->balance < $total_price) {
                throw new DisplayException("You don\47t have enough founds on your account to buy this server.");
            }
            foreach ($carts as $cart) {
                $products = DB::table("products")->where("id", "=", $cart->product_id)->get();
                foreach ($products as $product) {
                    $eggs = DB::table("eggs")->where("id", "=", $product->egg_id)->get();
                    foreach ($eggs as $egg) {
                        $nests = DB::table("nests")->where("id", "=", $egg->nest_id)->get();
                        foreach ($nests as $nest) {
                            if (!$nest || !$egg) {
                                throw new DisplayException("No Egg/Nest");
                            }
                            $node_id = array_random(explode(",", $product->node_id));

                            $allocation = DB::table("allocations")
                            ->where("server_id", "=", null)
                            ->where("node_id", "=", $node_id)
                            ->inRandomOrder()
                            ->first();

                            /*if ($promotional_code_type == 1) {
                                $amount = $Ca82347b0ea74027 / $f7ccdf8fe115e9b8 + $product->price;
                            } elseif ($promotional_code_type == 0) {
                                $amount = $product->price;
                            }*/

                            $amount = $product->price;


                            if (!$allocation) {
                                throw new DisplayException("We are sorry but at the moment there is no space left on our servers.");
                            }

                            $image = json_decode($egg->docker_images);

                            $data = [
                                "name" => $product->name, 
                                "owner_id" => $request->user()->id, 
                                "egg_id" => $egg->id, 
                                "nest_id" => $nest->id, 
                                "node_id" => $node_id, 
                                "allocation_id" => $allocation->id, 
                                "environment" => [], 
                                "memory" => $product->memory, 
                                "disk" => $product->disk, 
                                "cpu" => $product->cpu, 
                                "swap" => 0, 
                                "io" => $product->io, 
                                "database_limit" => $product->database_limit, 
                                "allocation_limit" => $product->allocation_limit, 
                                "backup_limit" => $product->backup_limit, 
                                "image" => $image[0], 
                                "startup" => $egg->startup, 
                                "start_on_completion" => true
                            ];

                            foreach (DB::table("egg_variables")->where('egg_id', '=', $egg->id)->get() as $var) {
                                $key = "v{$nest->id}-{$egg->id}-{$var->env_variable}";
                                $data["environment"][$var->env_variable] = $request->get($key, $var->default_value);
                            }

                            $server = $this->creationService->handle($data);
                            $server->monthly_cost = $product->price;
                            $server->renewal_date = date("Y-m-d h:m:s", strtotime(date("Y-m-d h:m:s") . " +1 month"));
                            $server->save();

                            $invoice = new Invoice;
                            $invoice->amount = $amount;
                            $invoice->reason = $product->name;
                            $invoice->user_id = $request->user()->id;
                            $invoice->billing_first_name = $request->user()->billing_first_name;
                            $invoice->billing_last_name = $request->user()->billing_last_name;
                            $invoice->billing_address = $request->user()->billing_address;
                            $invoice->billing_city = $request->user()->billing_city;
                            $invoice->billing_country = $request->user()->billing_country;
                            $invoice->billing_zip = $request->user()->billing_zip;
                            $invoice->save();

                            DB::table("users")->where("id", "=", auth()->user()->id)->update([
                            	"balance" => auth()->user()->balance - $total_price
                            ]);
                            DB::table("carts")->where("user_id", "=", $request->user()->id)->where("id", "=", $product->id)->delete();

                                // Notifications
                                /*$users = User::where('id', '=', $request->user()->id)->get();
                                foreach ($users as $user) {
                                    $user->notify(new NewOrder($invoice, $server));

                                }*/

                                /*if ($c589831893f8f89d == 1) {
                                    foreach (Session::get("cart") as $cart) {
                                        if (end($cart)["name"] == "Promotional Code") {
                                            $promotional_codes = DB::table("promotional_codes")
                                            ->where("id", "=", end($cart)["code"])
                                            ->get();
                                            foreach ($promotional_codes as $promotional_code) {
                                                $uses = $promotional_code->uses + 1;
                                                DB::table("promotional_codes")
                                                ->where("id", "=", end($cart)["code"])
                                                ->update([
                                                    "uses" => $uses
                                                ]);
                                            }
                                        }
                                    }
                                }*/

                        }
                    }
                }
            }
        }

        return [
            'success' => true,
            'data' => [],
        ];

    }

    public function extend($id)
    {
        $server = DB::table('servers')->where('uuidShort', '=', $id)->first();
        $billing = DB::table('billing')->get();

        $itemArray = array($server->id=>array(
            'name' => 'Extend Server '. $server->id,
            'code' => $server->uuidShort,
            'quantity' => '1',
            'price' => $server->monthly_cost,
            'billing' => $billing
        ));

        $inArray = 0;

        if (Session::has('cart-extend') == true) {
            foreach (Session::get('cart-extend') as $cart) {
                if (end($cart)['name'] == 'Extend Server '. $server->id) {
                    $inArray = 1;
                }
            }
        }

        if ($inArray == 0) {
            Session::push('cart-extend', $itemArray);
        }

        if (Session::has('cart-extend') == false) {
            return redirect()->back();
        }

        $billing = DB::table('billing')->get();

        return view('base.billing.extend', [
            'total_price' => 0,
            'billing' => $billing,
            'server' => $server,
        ]);
    }

    public function extend_server(Request $request, $id)
    {
        $server = DB::table('servers')->where('uuidShort', '=', $id)->first();

        if (auth()->user()->balance < $server->monthly_cost) {
            return redirect()->back()->withErrors(trans('base.errors.deploy.founds', ['cost' => $server->monthly_cost]));
        }

        $invoice = new Invoice;
        $invoice->amount = $server->monthly_cost;
        $invoice->reason = $server->name;
        $invoice->user_id = $request->user()->id;
        $invoice->billing_first_name = $request->user()->billing_first_name;
        $invoice->billing_last_name = $request->user()->billing_last_name;
        $invoice->billing_address = $request->user()->billing_address;
        $invoice->billing_city = $request->user()->billing_city;
        $invoice->billing_country = $request->user()->billing_country;
        $invoice->billing_zip = $request->user()->billing_zip;
        $invoice->save();

        // Notifications
        $users = User::where('id', '=', $request->user()->id)->get();
        foreach ($users as $user) {
            $user->notify(new NewOrder($invoice, $server));

        }

        DB::table('users')->where('id', '=', $request->user()->id)->update([
            'balance' => $request->user()->balance - $server->monthly_cost,
        ]);

        DB::table('servers')->where('id', '=', $server->id)->update([
            'renewal_date' => date("Y-m-d h:m:s", strtotime($server->renewal_date ." +1 month" )),
        ]);

        Session::forget('cart-extend');

        return redirect(route('server.index', $server->uuidShort));
    }


    public function promotional(Request $request)
    {
        $total_price = 0;
        foreach (Session::get("cart") as $cart) {
            if (end($cart)["name"] == "Promotional Code") {
                return redirect()->back()->withErrors("You are already using a promotional code. Clear your basket to use another one.");
            }
            if (end($cart)["name"] !== "Promotional Code") {
                $total_price = $total_price + end($cart)["price"];
            }
        }
        $input = $request->code;
        $codes = DB::table("promotional_codes")->get();
        foreach ($codes as $code) {
            if ($code->code == $input) {
                if ($total_price < $code->min_basket) {
                    return redirect()->back()->withErrors("To use this promotion code, your basket has to have a minimum value of \x24" . $code->min_basket);
                }
                if ($code->max_basket !== 0.0) {
                    if ($total_price > $code->max_basket) {
                        return redirect()->back()->withErrors("To use this promotion code, your basket is allowed to have a value of \x24" . $code->max_basket);
                    }
                }
                if (now() > $code->lasts_till) {
                    if ($code->lasts_till !== "0000-00-00 00:00:00") {
                        return redirect()->back()->withErrors("This coupon code has expired");
                    }
                }
                if ($code->max_uses !== null) {
                    if ($code->max_uses - $code->uses <= 0) {
                        return redirect()->back()->withErrors("this promotion code reached maximum uses (" . $code->max_uses . "/" . $code->uses . ")");
                    }
                }
                $total_price = 0;
                foreach (Session::get("cart") as $cart) {
                    $total_price = $total_price + end($cart)["price"];
                }
                $total_price = ($total_price / 100 * $code->percentage - $code->amount * -1) * -1;
                $itemArray = array($code->id => array(
                    "name" => "Promotional Code", 
                    "code" => $code->id, 
                    "quantity" => "1", 
                    "price" => $total_price
                ));
                Session::push("cart", $itemArray);
            }
        }
        return redirect()->back();
    }

    private function getAllocationId($memory = 0, $attempt = 0)
    {
        if ($attempt > 6) return null;

        $node = Node::where("nodes.public", true)
        ->where("nodes.maintenance_mode", false)
        ->whereRaw("nodes.memory - ? > (SELECT IFNULL(SUM(servers.memory), 0) FROM servers WHERE servers.node_id = nodes.id)", [$memory])
        ->whereRaw("nodes.disk > (SELECT IFNULL(SUM(servers.disk), 0) FROM servers WHERE servers.node_id = nodes.id)")
        ->first();
        
        if (!$node) return false;

        $allocation = $node->allocations()->where("server_id", null)->inRandomOrder()->first();

        if (!$allocation) return getAllocationId($memory, $attempt + 1);

        return $allocation->id;
    }
}

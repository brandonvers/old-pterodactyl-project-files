<?php

namespace Pterodactyl\Http\Controllers\Base;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Traits\Controllers\JavascriptInjection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KnowledgebaseController extends Controller
{
    
    use JavascriptInjection;

    /**
     * @var \Prologue\Alerts\AlertsMessageBag
     */
    private $alert;

    protected $cache;

    /**
     * LocationController constructor.
     *
     * @param \Prologue\Alerts\AlertsMessageBag $alert
     */
    public function __construct(AlertsMessageBag $alert)
    {
        $this->middleware("auth");
        $this->alert = $alert;
    }

    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $categorys = DB::table('knowledgebasecategory')->paginate(10);
        $settings = DB::table('knowledgebasesettings')->get();
        $questions = DB::table('knowledgebase')->paginate(10);

        return view('knowledgebase.index', [
            'categorys' => $categorys,
            'questions' => $questions,            
            'settings' => $settings
        ]);
    }

    public function list(Request $request, $id)
    {
        $id = (int) $id;

        $categorys = DB::table('knowledgebasecategory')->get();
        $settings = DB::table('knowledgebasesettings')->get();
        $questions = DB::table('knowledgebase')->where('category', '=', $id)->paginate(10);
        
        return view('knowledgebase.list', [
            'questions' => $questions,
            'categorys' => $categorys,
            'settings' => $settings
        ]);
    }

    public function page(Request $request, $id)
    {
        $id = (int) $id;

        $time = date("Y", time()); 

        $settings = DB::table('knowledgebasesettings')->get();
        $question = DB::table('knowledgebase')->where('id', '=', $id)->get();
        if (count($question) < 1) {
            return redirect()->route('knowledgebase.page');
        }

        return view('knowledgebase.page', [
            'question' => $question[0],
            'time' => $time,
            'settings' => $settings
        ]);
    }

}


<?php

namespace Pterodactyl\Http\Controllers\Admin;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KnowledgebaseController extends Controller
{
    
    private $alert;


    public function __construct(AlertsMessageBag $alert)
    {
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

        return view('admin.knowledgebase.index', [
            'categorys' => $categorys,
            'settings' => $settings
        ]);
    }

    public function settings(Request $request)
    {
        $this->validate($request, [
            'category' => 'required',
            'author' => 'required',
        ]);

        $category = trim(strip_tags($request->input('category')));
        $author = trim($request->input('author'));

        DB::table('knowledgebasesettings')->update([
            'category' => $category,
            'author' => $author,
        ]);
        $this->alert->success('You have successfully updated the knowledgebase settings')->flash();

        return redirect()->route('admin.knowledgebase');
    }

    public function question_index(Request $request): View
    {
        $questions = DB::table('knowledgebase')->paginate(10);
        $categories = DB::table('knowledgebasecategory')->get();
        $settings = DB::table('knowledgebasesettings')->get();


        return view('admin.knowledgebase.questions.index', [
            'questions' => $questions,
            'categories' => $categories,
            'settings' => $settings
        ]);
    }
    
    public function question_new(Request $request): View
    {
        $category = DB::table('knowledgebasecategory')->get();

        return view('admin.knowledgebase.questions.new', [
            'category' => $category
        ]);
    }
    
    public function question_create(Request $request)
    {
        $this->validate($request, [
            'Subject' => 'required|max:75',
            'Created' => 'required|max:50',
            'category' => 'required',
            'Answer' => 'required'
        ]);

        $Subject = trim(strip_tags($request->input('Subject')));
        $Answer = trim($request->input('Answer'));
        $Created = trim($request->input('Created'));
        $category = trim($request->input('category'));

        DB::table('knowledgebase')->insert([
            'subject' => $Subject,
            'author' => $Created,
            'information' => $Answer,
            'category' => $category,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        $this->alert->success('You have successfully creadted the question')->flash();

        return redirect()->route('admin.knowledgebase.questions.index');
    }


    public function question_edit(Request $request, $id)
    {
        $id = (int) $id;

        $category = DB::table('knowledgebasecategory')->get();

        $question = DB::table('knowledgebase')->where('id', '=', $id)->get();
        if (count($question) < 1) {
            return redirect()->route('admin.knowledgebase.questions.edit');
        }

        return view('admin.knowledgebase.questions.edit', [
            'question' => $question[0],
            'category' => $category
        ]);

    }

    public function question_update(Request $request, $id)
    {
        $id = (int) $id;

        $this->validate($request, [
            'Subject' => 'required|max:75',
            'Created' => 'required|max:25',
            'category' => 'required',
            'Answer' => 'required'
        ]);

        $Subject = trim(strip_tags($request->input('Subject')));
        $Answer = trim($request->input('Answer'));
        $Created = trim($request->input('Created'));
        $Category = trim($request->input('category'));

        DB::table('knowledgebase')->where('id', '=', $id)->update([
            'subject' => $Subject,
            'author' => $Created,
            'information' => $Answer,
            'category' => $Category,
            'updated_at' => \Carbon\Carbon::now()
        ]);
        $this->alert->success('You have successfully edited this question')->flash();

        return redirect()->route('admin.knowledgebase.questions.index');
    }

    public function question_delete(Request $request)
    {

        $id = (int) $request->input('id', '');

        $question = DB::table('knowledgebase')->where('id', '=', $id)->get();
        if (count($question) < 1) {
            return response()->json(['error' => 'question not found.'])->setStatusCode(500);
        }

        DB::table('knowledgebase')->where('id', '=', $id)->delete();

        return response()->json(['success' => true]);

    }

    // Categories

    public function category_index(Request $request): View
    {
        $categories = DB::table('knowledgebasecategory')->paginate(10);
        $settings = DB::table('knowledgebasesettings')->get();

        return view('admin.knowledgebase.category.index', [
            'categories' => $categories,
            'settings' => $settings
        ]);
    }
    
    public function category_new(Request $request): View
    {
        return view('admin.knowledgebase.category.new');
    }

    public function category_create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
        ]);

        $name = trim(strip_tags($request->input('name')));
        $description = trim($request->input('description'));

        DB::table('knowledgebasecategory')->insert([
            'name' => $name,
            'description' => $description,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        $this->alert->success('You have successfully creadted the category')->flash();

        return redirect()->route('admin.knowledgebase.category.index');
    }

    public function category_edit(Request $request, $id)
    {
        $id = (int) $id;

        $category = DB::table('knowledgebasecategory')->where('id', '=', $id)->get();
        if (count($category) < 1) {
            return redirect()->route('admin.knowledgebase.category.edit');
        }

        return view('admin.knowledgebase.category.edit', [
            'category' => $category[0]
        ]);

    }

    public function category_update(Request $request, $id)
    {
        $id = (int) $id;

        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
        ]);

        $name = trim(strip_tags($request->input('name')));
        $description = trim($request->input('description'));

        DB::table('knowledgebasecategory')->where('id', '=', $id)->update([
            'name' => $name,
            'description' => $description,
            'updated_at' => \Carbon\Carbon::now()
        ]);
        $this->alert->success('You have successfully edited this category')->flash();

        return redirect()->route('admin.knowledgebase.category.index');
    }

    public function category_delete(Request $request)
    {

        $id = (int) $request->input('id', '');

        $category = DB::table('knowledgebasecategory')->where('id', '=', $id)->get();
        if (count($category) < 1) {
            return response()->json(['error' => 'category not found.'])->setStatusCode(500);
        }

        DB::table('knowledgebasecategory')->where('id', '=', $id)->delete();

        return response()->json(['success' => true]);
    }
}
<?php

namespace Pterodactyl\Http\Controllers\Api\Client;

use Pterodactyl\Http\Requests\Api\Client\KnowledgebaseRequest;
use Illuminate\Support\Facades\DB;
use Pterodactyl\Exceptions\DisplayException;

class KnowledgebaseController extends ClientApiController
{
    

    public function __construct()
    {
        $this->middleware("auth");
    }

    /**
     * @param Request $request
     * @return View
     */
    public function index(KnowledgebaseRequest $request): array
    {
        $categories = DB::table('knowledgebasecategory')->get();
        $settings = DB::table('knowledgebasesettings')->get();
        $questions = DB::table('knowledgebase')->get();

        return [
            'success' => true,
            'data' => [
                'categories' => $categories,
                'questions' => $questions,
                'settings' => $settings,
            ],
        ];

    }

    public function list(KnowledgebaseRequest $request, $id): array
    {
        $id = (int) $id;

        $categories = DB::table('knowledgebasecategory')->get();
        $settings = DB::table('knowledgebasesettings')->get();
        $questions = DB::table('knowledgebase')->where('category', '=', $id)->get();

        foreach ($questions as $key => $question) {
            $questions[$key]->categoryname = DB::table('knowledgebasecategory')->select(['id', 'name',])->where('id', '=', $question->category)->first();
        }

        return [
            'success' => true,
            'data' => [
                'questions' => $questions,
                'categories' => $categories,
                'settings' => $settings,
            ],
        ];

    }

    public function page(KnowledgebaseRequest $request, $id): array
    {
        $id = (int) $id;

        $settings = DB::table('knowledgebasesettings')->get();
        $questions = DB::table('knowledgebase')->where('id', '=', $id)->get();

        foreach ($questions as $key => $question) {
            $questions[$key]->categoryname = DB::table('knowledgebasecategory')->select(['id', 'name',])->where('id', '=', $question->category)->first();
        }

        if (count($questions) < 1) {
            throw new DisplayException('Question not found.');
        }

        return [
            'success' => true,
            'data' => [
                'questions' => $questions,
                'settings' => $settings,
            ],
        ];

    }

}


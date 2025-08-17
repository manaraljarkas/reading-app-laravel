<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SearchService;


class SearchController extends Controller
{
    public function __construct(protected SearchService $searchService) {}

    public function index(Request $request)
    {
        $keyword = $request->q;
        $results = $this->searchService->search($keyword);

        return response()->json($results);
    }
}

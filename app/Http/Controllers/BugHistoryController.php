<?php

namespace App\Http\Controllers;

use App\Models\Bug;
use App\Models\BugHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BugHistoryController extends Controller
{
    public function index(int $id): JsonResponse
    {
        $bugsHistory = BugHistory::where('bug_id', $id)->with('user')->orderBy('created_at', 'desc')->limit(3)->get();

        return response()->json(['data' => $bugsHistory]);
    }
}

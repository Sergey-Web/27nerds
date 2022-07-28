<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\LeagueService;

class LeaguesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $startTimestamp = null;

        if (is_numeric($request->get('start_timestamp'))) {
            $startTimestamp = (int) $request->get('start_timestamp');
        }

        return response()->json(['leagueIds' => (new LeagueService())->getList($startTimestamp)]);
    }

    public function show(int $leagueId): JsonResponse
    {
        return response()->json(['leagueIds' => (new LeagueService())->getLeagueForId($leagueId)]);
    }
}

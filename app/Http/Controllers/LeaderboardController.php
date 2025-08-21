<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Services\LeaderboardService;

class LeaderboardController extends Controller
{
    public function index(LeaderboardService $svc)
    {
        return $svc->top();
    }
}

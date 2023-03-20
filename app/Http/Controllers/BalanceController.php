<?php

namespace App\Http\Controllers;

use App\Http\UserConstant;
use App\Models\UsageLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BalanceController extends Controller
{
    public function getBalance(): JsonResponse
    {
        $userId = intval(config(UserConstant::USER_ID_KEY));
        Log::info("Start to get balance", ["user" => $userId]);
        $balance = UsageLog::getUserBalance($userId);
        Log::info("Got balance", ["user" => $userId, "balance" => intval($balance),]);
        return response()
            ->json(array(
                "balance" => $balance,
            ));
    }
}

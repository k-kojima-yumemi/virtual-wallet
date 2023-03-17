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
        /** @noinspection PhpUndefinedMethodInspection (`where` should be callable.) */
        $balance = UsageLog::where("user_id", $userId)->sum("changed_amount");
        Log::info("Got balance", ["user" => $userId, "balance" => intval($balance),]);
        return response()
            ->json(array(
                "balance" => intval($balance),
            ));
    }
}

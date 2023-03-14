<?php

namespace App\Http\Controllers;

use App\Http\UserConstant;
use App\Models\UsageLog;
use Illuminate\Http\JsonResponse;

class BalanceController extends Controller
{
    public function getBalance(): JsonResponse
    {
        $userId = intval(config(UserConstant::USER_ID_KEY));
        /** @noinspection PhpUndefinedMethodInspection (`where` should be callable.) */
        $balance = UsageLog::where("user_id", $userId)->sum("changed_amount");
        return response()
            ->json(array(
                "balance" => intval($balance),
            ));
    }
}

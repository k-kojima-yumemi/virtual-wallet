<?php

namespace App\Http\Controllers;

use App\Models\UsageLog;
use Illuminate\Http\JsonResponse;

class BalanceController extends Controller
{
    public function getBalance(): JsonResponse
    {
        $userId = intval(config("app.user_id"));
        /** @noinspection PhpUndefinedMethodInspection (`where` should be callable.) */
        $balance = UsageLog::where("user_id", $userId)->sum("changed_amount");
        return response()
            ->json(array(
                "balance" => intval($balance),
            ));
    }
}

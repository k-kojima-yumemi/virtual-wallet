<?php

namespace App\Http\Controllers;

use App\Models\UsageLog;
use JetBrains\PhpStorm\ArrayShape;

class BalanceController extends Controller
{
    #[ArrayShape(["balance" => "int"])]
    public function getBalance(): array
    {
        $userId = intval(config("app.user_id"));
        /** @noinspection PhpUndefinedMethodInspection (`where` should be callable.)*/
        $balance = UsageLog::where("user_id", $userId)->sum("changed_amount");
        return array(
            "balance" => intval($balance),
        );
    }
}

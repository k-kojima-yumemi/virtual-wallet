<?php

namespace App\Http\Controllers;

use App\Models\UsageLog;
use App\UserConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\ArrayShape;

class BalanceController extends Controller
{
    #[ArrayShape(["balance" => "int"])]
    public function getBalance(Request $request): array
    {
        // テスト時のみHeaderでユーザーを区別するようにする
        if (DB::getDatabaseName() == "testing") {
            // テスト時のユーザー指定に使用
            $userId = intval($request->header("user_id", UserConstant::USER_ID));
        } else {
            // 通常の際には固定のユーザーとする
            $userId = UserConstant::USER_ID;
        }
        /** @noinspection PhpUndefinedMethodInspection (`where` should be callable.)*/
        $balance = UsageLog::where("user_id", $userId)->sum("changed_amount");
        return array(
            "balance" => intval($balance),
        );
    }
}

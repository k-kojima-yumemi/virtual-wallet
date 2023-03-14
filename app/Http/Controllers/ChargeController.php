<?php

namespace App\Http\Controllers;

use App\Http\UserConstant;
use App\Models\UsageLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChargeController extends Controller
{
    private array $validationRules = [
        "amount" => ["required", "integer", "numeric", "min:1"]
    ];

    public function charge(Request $request): JsonResponse
    {
        $validator = $this->getValidationFactory()->make($request->all(), $this->validationRules);
        if ($validator->fails()) {
            return response()
                ->json(array(
                    "reason" => "Invalid request.",
                ), Response::HTTP_BAD_REQUEST);
        }

        $userId = intval(config(UserConstant::USER_ID_KEY));
        // 新しいUsageLogの作成
        $chargeValue = intval($request->get("amount"));
        $usage = new UsageLog([
            "user_id" => $userId,
            "used_at" => now(),
            "changed_amount" => $chargeValue,
            "description" => "チャージ"
        ]);
        // UsageLogをDBに保存
        $usage->save();

        // 返却値用の残高取得
        /** @noinspection PhpUndefinedMethodInspection (`where` should be callable.) */
        $balance = UsageLog::where("user_id", $userId)->sum("changed_amount");
        return response()
            ->json(array(
                "balance" => $balance,
            ));
    }
}

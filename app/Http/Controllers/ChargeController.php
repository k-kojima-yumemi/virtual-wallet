<?php

namespace App\Http\Controllers;

use App\Models\UsageLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseCodes;

class ChargeController extends Controller
{
    private array $validationRules = [
        "amount" => ["required", "integer", "numeric", "min:1"]
    ];

    public function charge(Request $request): Response
    {
        $validator = $this->getValidationFactory()->make($request->all(), $this->validationRules);
        if($validator->fails()){
            return response("Invalid request", ResponseCodes::HTTP_BAD_REQUEST);
        }

        $userId = intval(config("app.user_id", 100));
        // 新しいUsageLogの作成
        $chargeValue = intval($request->get("amount"));
        $usage = new UsageLog([
            "user_id"=>$userId,
            "used_at"=>now(),
            "changed_amount"=>$chargeValue,
            "description"=>"チャージ"
        ]);
        // UsageLogをDBに保存
        $usage->save();

        // 返却値用の残高取得
        /** @noinspection PhpUndefinedMethodInspection (`where` should be callable.)*/
        $balance = UsageLog::where("user_id", $userId)->sum("changed_amount");
        return response(array(
            "balance"=>$balance
        ));
    }
}

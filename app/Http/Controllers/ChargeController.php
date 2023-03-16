<?php

namespace App\Http\Controllers;

use App\Http\UserConstant;
use App\Models\UsageLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ChargeController extends Controller
{
    private array $validationRules = [
        "amount" => ["required", "integer", "numeric", "min:1"]
    ];

    /**
     * @throws ValidationException
     */
    public function charge(Request $request): JsonResponse
    {
        // バリデーション。失敗するとここで422か302が返される。
        $validatedData = $this->validate($request, $this->validationRules);

        $userId = intval(config(UserConstant::USER_ID_KEY));
        // 新しいUsageLogの作成
        $chargeValue = intval($validatedData["amount"]);
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
        $returnValue = array(
            "balance" => $balance,
        );
        // チャージ後の残高がマイナスならチャージを促すメッセージを入れる。
        if ($balance <= 0) {
            $returnValue["message"] = "チャージしてください";
        }
        return response()
            ->json($returnValue);
    }
}

<?php

namespace App\Http\Controllers;

use App\ConstMessages;
use App\Http\UserConstant;
use App\Models\UsageLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        Log::debug("Validate request of charging", ["request" => $request->all()]);
        // バリデーション。失敗するとここで422か302が返される。
        $validatedData = $this->validate($request, $this->validationRules);

        $userId = intval(config(UserConstant::USER_ID_KEY));
        Log::info("Start to charge", ["user" => $userId,]);
        // 新しいUsageLogの作成
        $chargeValue = intval($validatedData["amount"]);
        $usage = new UsageLog([
            "user_id" => $userId,
            "used_at" => now(),
            "changed_amount" => $chargeValue,
            "description" => ConstMessages::CHARGE_DESCRIPTION,
        ]);
        // UsageLogをDBに保存
        $usage->save();
        Log::debug("Charged", ["user" => $userId, "amount" => $chargeValue,]);

        // 返却値用の残高取得
        $balance = UsageLog::getUserBalance($userId);
        $returnValue = array(
            "balance" => $balance,
        );
        // チャージ後の残高がマイナスならチャージを促すメッセージを入れる。
        if ($balance <= 0) {
            $returnValue["message"] = ConstMessages::CHARGE_SUGGESTION_MESSAGE;
        }
        Log::info("Return response for charging", [
            "user" => $userId,
            "before" => $balance - $chargeValue,
            "charged" => $chargeValue,
            "return" => $returnValue,
        ]);
        return response()
            ->json($returnValue);
    }
}

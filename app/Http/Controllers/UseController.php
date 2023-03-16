<?php

namespace App\Http\Controllers;

use App\ConstMessages;
use App\Models\UsageLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class UseController extends Controller
{
    private array $validationRules = [
        "amount" => ["required", "integer", "numeric", "min:1"],
        "description" => ["required", "string"],
    ];

    /**
     * @throws ValidationException
     */
    public function use(Request $request): JsonResponse
    {
        Log::debug("Validate request of using", ["request" => $request->all()]);
        // 入力値のチェック。要件を満たしていない場合は422
        $validated = $this->validate($request, $this->validationRules);

        $userId = intval(config("app.user_id"));
        Log::info("Start to use", ["user" => $userId,]);
        $balance = UsageLog::getUserBalance($userId);
        // 残高が0円以下なら使用しない。チャージを促して400を返す。
        if ($balance <= 0) {
            Log::info("Reject use because of insufficient balance", [
                "user" => $userId,
                "balance" => $balance,
            ]);
            return response()
                ->json(array(
                    "message" => ConstMessages::BALANCE_MINUS_MESSAGE
                ), Response::HTTP_BAD_REQUEST);
        }

        // 使用できる。ログをDBに残す。
        $useValue = intval($validated["amount"]);
        $usage = new UsageLog([
            "user_id" => $userId,
            "used_at" => now(),
            "changed_amount" => -$useValue,
            "description" => $validated["description"],
        ]);
        // UsageLogをDBに保存
        $usage->save();
        Log::debug("Used", [
            "user" => $userId,
            "amount" => $useValue,
            "description" => $validated["description"]
        ]);

        // 使用後の残高の計算。
        $newBalance = $balance - $useValue;
        $returnValue = array(
            "balance" => $newBalance
        );
        // 使用後の残高がマイナスならチャージを促すメッセージを入れる。
        if ($newBalance <= 0) {
            $returnValue["message"] = ConstMessages::CHARGE_SUGGESTION_MESSAGE;
        }
        Log::info("Return response for using", [
            "user" => $userId,
            "before" => $balance,
            "used" => $useValue,
            "return" => $returnValue,
        ]);
        return response()
            ->json($returnValue);
    }
}

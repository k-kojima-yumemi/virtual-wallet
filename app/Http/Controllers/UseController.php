<?php

namespace App\Http\Controllers;

use App\ConstMessages;
use App\Http\UserConstant;
use App\Models\UsageLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        // 入力値のチェック。要件を満たしていない場合は422
        $validated = $this->validate($request, $this->validationRules);

        $userId = intval(config(UserConstant::USER_ID_KEY));
        /** @noinspection PhpUndefinedMethodInspection (`where` should be callable.) */
        $balance = UsageLog::where("user_id", $userId)->sum("changed_amount");
        // 残高が0円以下なら使用しない。チャージを促して400を返す。
        if ($balance <= 0) {
            return response()
                ->json(array(
                    "message" => ConstMessages::BALANCE_MINUS_MESSAGE
                ), Response::HTTP_BAD_REQUEST);
        }

        // 使用できる。ログをDBに残す。
        $useValue = intval($validated["amount"]);
        $usage = UsageLog::create(
            $userId, now(), -$useValue, $validated["description"]
        );
        // UsageLogをDBに保存
        $usage->save();

        // 使用後の残高の計算。
        $newBalance = $balance - $useValue;
        $returnValue = array(
            "balance" => $newBalance
        );
        // 使用後の残高がマイナスならチャージを促すメッセージを入れる。
        if ($newBalance <= 0) {
            $returnValue["message"] = ConstMessages::CHARGE_SUGGESTION_MESSAGE;
        }
        return response()
            ->json($returnValue);
    }
}

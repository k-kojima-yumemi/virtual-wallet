<?php

namespace App\Http\Controllers;

use App\Models\UsageLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UseController extends Controller
{
    private array $validationRules = [
        "amount" => ["required", "integer", "numeric", "min:1"],
        "description" => ["required", "string"],
    ];

    public function use(Request $request): JsonResponse
    {
        // 入力値のチェック。要件を満たしていない場合は400
        $validator = $this->getValidationFactory()->make($request->all(), $this->validationRules);
        if ($validator->fails()) {
            return response()
                ->json(array(
                    "message" => "Invalid request.",
                ), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $userId = intval(config("app.user_id"));
        /** @noinspection PhpUndefinedMethodInspection (`where` should be callable.) */
        $balance = UsageLog::where("user_id", $userId)->sum("changed_amount");
        // 残高が0円以下なら使用しない。チャージを促して400を返す。
        if ($balance <= 0) {
            return response()
                ->json(array(
                    "message" => "残高がマイナスです。チャージしてください。"
                ), Response::HTTP_BAD_REQUEST);
        }

        // 使用できる。ログをDBに残す。
        $useValue = intval($request->get("amount"));
        $usage = new UsageLog([
            "user_id" => $userId,
            "used_at" => now(),
            "changed_amount" => -$useValue,
            "description" => $request->get("description"),
        ]);
        // UsageLogをDBに保存
        $usage->save();

        // 使用後の残高の計算。
        $newBalance = $balance - $useValue;
        $returnValue = array(
            "balance" => $newBalance
        );
        // 使用後の残高がマイナスならチャージを促すメッセージを入れる。
        if ($newBalance <= 0) {
            $returnValue["message"] = "チャージしてください";
        }
        return response()
            ->json($returnValue);
    }
}

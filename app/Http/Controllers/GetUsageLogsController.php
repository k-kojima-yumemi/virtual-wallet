<?php

namespace App\Http\Controllers;

use App\Http\UserConstant;
use App\Models\UsageLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GetUsageLogsController extends Controller
{
    private array $validationRules = [
        "from" => ["nullable", "date"],
        "to" => ["nullable", "date"],
    ];

    /**
     * @throws ValidationException
     */
    public function getUsageLogs(Request $request): JsonResponse
    {
        $validated = $this->validate($request, $this->validationRules);
        $userId = intval(config(UserConstant::USER_ID_KEY));
        $query = DB::table("usage_logs")->where(UsageLog::KEY_USER_ID, $userId);
        if (array_key_exists("from", $validated)) {
            $query = $query->where(UsageLog::KEY_USED_AT, ">=", $validated["from"]);
        }
        if (array_key_exists("to", $validated)) {
            $query = $query->where(UsageLog::KEY_USED_AT, "<", $validated["to"]);
        }
        $logs = $query->get([UsageLog::KEY_USED_AT, UsageLog::KEY_CHANGED_AMOUNT, UsageLog::KEY_DESCRIPTION]);
        return response()
            ->json(array(
                "logs" => $logs,
            ));
    }
}

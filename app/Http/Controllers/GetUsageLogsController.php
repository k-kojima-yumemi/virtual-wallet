<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $userId = intval(config("app.user_id"));
        Log::info("Start to get usage logs", ["user" => $userId,]);
        $query = DB::table("usage_logs")->where("user_id", $userId);
        if (array_key_exists("from", $validated)) {
            Log::debug("Apply 'from' to get logs", ["user" => $userId, "from" => $validated["from"]]);
            $query = $query->where("used_at", ">=", $validated["from"]);
        }
        if (array_key_exists("to", $validated)) {
            Log::debug("Apply 'to' to get logs", ["user" => $userId, "to" => $validated["to"]]);
            $query = $query->where("used_at", "<", $validated["to"]);
        }
        $logs = $query->get(["used_at", "changed_amount", "description"]);
        Log::info("Return usage logs", ["user" => $userId, "log_length" => $logs->count()]);
        return response()
            ->json(array(
                "logs" => $logs,
            ));
    }
}

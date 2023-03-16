<?php

namespace App\Http\Controllers;

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
        $userId = intval(config("app.user_id"));
        $query = DB::table("usage_logs")->where("user_id", $userId);
        if (array_key_exists("from", $validated)) {
            $query = $query->where("used_at", ">=", $validated["from"]);
        }
        if (array_key_exists("to", $validated)) {
            $query = $query->where("used_at", "<", $validated["to"]);
        }
        $logs = $query->get(["used_at", "changed_amount", "description"]);
        return response()
            ->json(array(
                "logs" => $logs,
            ));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetUsageLogsController extends Controller
{
    public function getUsageLogs(Request $request): JsonResponse
    {
        $userId = intval(config("app.user_id"));
        $minTime = $request->query("from");
        $maxTime = $request->query("to");
        $query = DB::table("usage_logs")->where("user_id", $userId);
        if ($minTime != null) {
            $query = $query->where("used_at", ">=", $minTime);
        }
        if ($maxTime != null) {
            $query = $query->where("used_at", "<", $maxTime);
        }
        $logs = $query->get(["id", "used_at", "changed_amount", "description"]);
        return response()
            ->json(array(
                "logs" => $logs,
            ));
    }
}

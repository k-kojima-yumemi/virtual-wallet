<?php

namespace App\Models;

use Database\Factories\UsageLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageLog extends Model
{
    use HasFactory;

    protected static function newFactory(): UsageLogFactory
    {
        return UsageLogFactory::new();
    }

    protected $fillable = [
        "user_id",
        "used_at",
        "changed_amount",
        "description",
    ];

    public static function getUserBalance(int $userId): int
    {
        /** @noinspection PhpUndefinedMethodInspection (`where` should be callable.) */
        return intval(UsageLog::where("user_id", $userId)->sum("changed_amount"));
    }
}

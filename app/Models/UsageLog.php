<?php

namespace App\Models;

use Database\Factories\UsageLogFactory;
use DateTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageLog extends Model
{
    use HasFactory;

    protected static function newFactory(): UsageLogFactory
    {
        return UsageLogFactory::new();
    }

    public static function create(
        int $userId, DateTimeZone|string|null $usedAt,
        int $changedAmount, string $description,
    ): UsageLog
    {
        return new UsageLog([
            self::KEY_USER_ID => $userId,
            self::KEY_USED_AT => $usedAt,
            self::KEY_CHANGED_AMOUNT => $changedAmount,
            self::KEY_DESCRIPTION => $description,
        ]);
    }

    protected $fillable = [
        self::KEY_USER_ID,
        self::KEY_USED_AT,
        self::KEY_CHANGED_AMOUNT,
        self::KEY_DESCRIPTION,
    ];

    // Table keys
    const KEY_USER_ID = "user_id";
    const KEY_USED_AT = "used_at";
    const KEY_CHANGED_AMOUNT = "changed_amount";
    const KEY_DESCRIPTION = "description";
}

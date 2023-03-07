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

}

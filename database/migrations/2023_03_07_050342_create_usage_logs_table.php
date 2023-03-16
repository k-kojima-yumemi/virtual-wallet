<?php

use App\Models\UsageLog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger(UsageLog::KEY_USER_ID);
            $table->dateTime(UsageLog::KEY_USED_AT);
            $table->integer(UsageLog::KEY_CHANGED_AMOUNT);
            $table->string(UsageLog::KEY_DESCRIPTION);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_logs');
    }
};

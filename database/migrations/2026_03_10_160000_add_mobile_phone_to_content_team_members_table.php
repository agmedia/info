<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_team_members', function (Blueprint $table): void {
            $table->string('mobile_phone', 80)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('content_team_members', function (Blueprint $table): void {
            $table->dropColumn('mobile_phone');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('system_settings')) {
            return;
        }

        $setting = DB::table('system_settings')
            ->where('key', 'front_navigation_main')
            ->first(['id', 'value']);

        if ($setting) {
            // Navigation is CMS-managed. Absence of a Home item can be an
            // intentional editor choice, so an existing setting is authoritative.
            return;
        }

        // A missing setting is left for the CMS defaults/administrator to create;
        // this editorial migration must not manufacture navigation copy.
    }

    public function down(): void
    {
        // User-managed CMS navigation is intentionally preserved on rollback.
    }
};

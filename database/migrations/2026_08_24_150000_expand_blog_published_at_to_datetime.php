<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            // Preserve the clock value the application saw through the existing DB session.
            DB::statement('ALTER TABLE `content_blog_posts` MODIFY `published_at` DATETIME NULL');
        } elseif ($driver !== 'sqlite') {
            Schema::table('content_blog_posts', function (Blueprint $table): void {
                $table->dateTime('published_at')->nullable()->change();
            });
        }

        DB::table('content_blog_posts')
            ->where('is_active', true)
            ->whereNull('published_at')
            ->update(['published_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $hasOutOfRangeDates = DB::table('content_blog_posts')
                ->where('published_at', '>', '2038-01-19 03:14:07')
                ->exists();

            if ($hasOutOfRangeDates) {
                throw new RuntimeException('Cannot restore blog published_at to TIMESTAMP while dates after 2038-01-19 exist.');
            }

            // Use the same session semantics when converting back so visible values do not shift.
            DB::statement('ALTER TABLE `content_blog_posts` MODIFY `published_at` TIMESTAMP NULL');
        } elseif ($driver !== 'sqlite') {
            Schema::table('content_blog_posts', function (Blueprint $table): void {
                $table->timestamp('published_at')->nullable()->change();
            });
        }
    }
};

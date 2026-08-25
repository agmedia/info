<?php

use App\Models\Content\Team\TeamMember;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('content_team_members') || ! Schema::hasTable('content_team_member_translations')) {
            return;
        }

        $ana = TeamMember::query()->where('code', 'ana-mandic')->first();

        if (! $ana) {
            $anaId = DB::table('content_team_member_translations')
                ->whereRaw('LOWER(name) LIKE ?', ['%ana mandi%'])
                ->value('team_member_id');
            $ana = $anaId ? TeamMember::query()->find($anaId) : null;
        }

        if (! $ana) {
            return;
        }

        // The existing member row and every existing translation are CMS-owned,
        // including blank contact/profile fields. Only missing media may be seeded.

        $photoPath = public_path('front-theme/images/team/ana-mandic.png');

        if (
            Schema::hasTable('media')
            && is_file($photoPath)
            && ! $ana->getFirstMedia('team_photo')
        ) {
            $ana->addMedia($photoPath)
                ->preservingOriginal()
                ->usingName('Ana Mandić')
                ->usingFileName(basename($photoPath))
                ->withCustomProperties([
                    'alt' => [
                        'hr' => 'Ana Mandić',
                        'en' => 'Ana Mandić',
                    ],
                ])
                ->toMediaCollection('team_photo');
        }
    }

    public function down(): void
    {
        // Existing team members and editorial media are intentionally preserved.
    }
};

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

        $memberUpdates = [];
        if (trim((string) $ana->email) === '') {
            $memberUpdates['email'] = 'ana.mandic@alphacapitalis.com';
        }
        if (trim((string) $ana->linkedin_url) === '') {
            $memberUpdates['linkedin_url'] = 'https://www.linkedin.com/in/ana-mandic-phd-aa572b44';
        }
        if ($memberUpdates !== []) {
            $ana->forceFill($memberUpdates)->saveQuietly();
        }

        $translation = DB::table('content_team_member_translations')
            ->where('team_member_id', $ana->id)
            ->where('locale', 'hr')
            ->first();

        if ($translation) {
            $translationUpdates = ['updated_at' => now()];

            if ((string) $translation->name === 'Ana Mandić, PhD, ACCA') {
                $translationUpdates['name'] = 'Ana Mandić';
            }
            if ((string) $translation->position === 'Menadžer / Financijsko savjetovanje') {
                $translationUpdates['position'] = 'Menadžer / Savjetovanje';
            }
            if ((string) $translation->departments === 'Financijsko savjetovanje') {
                $translationUpdates['departments'] = 'Savjetovanje';
            }

            if (count($translationUpdates) > 1) {
                DB::table('content_team_member_translations')
                    ->where('id', $translation->id)
                    ->update($translationUpdates);
            }
        }

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

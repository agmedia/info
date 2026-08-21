<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->string('form_type', 64)->nullable()->after('status');
            $table->index(['form_type', 'status', 'created_at'], 'contact_messages_form_status_created_index');
        });

        DB::table('contact_messages')
            ->select(['id', 'subject', 'payload'])
            ->orderBy('id')
            ->chunkById(250, function ($messages): void {
                foreach ($messages as $message) {
                    $payload = json_decode((string) ($message->payload ?? ''), true);
                    $formType = trim((string) data_get($payload, 'form_type', ''));

                    if ($formType === '' && (string) $message->subject === 'EU Fondovi upitnik') {
                        $formType = 'eu_funds_questionnaire';
                    }

                    if ($formType === '') {
                        $formType = 'contact';
                    }

                    DB::table('contact_messages')
                        ->where('id', $message->id)
                        ->update(['form_type' => $formType]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->dropColumn('form_type');
        });
    }
};

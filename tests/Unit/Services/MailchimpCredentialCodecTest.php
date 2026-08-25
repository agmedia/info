<?php

namespace Tests\Unit\Services;

use App\Services\Newsletter\MailchimpCredentialCodec;
use Tests\TestCase;

class MailchimpCredentialCodecTest extends TestCase
{
    public function test_it_encrypts_new_credentials_and_decodes_them_server_side(): void
    {
        $codec = app(MailchimpCredentialCodec::class);
        $encoded = $codec->encode('secret-api-key-us21');

        $this->assertStringStartsWith(MailchimpCredentialCodec::PREFIX, $encoded);
        $this->assertStringNotContainsString('secret-api-key-us21', $encoded);
        $this->assertSame('secret-api-key-us21', $codec->decode($encoded));
        $this->assertSame($encoded, $codec->encode($encoded));
    }

    public function test_it_reads_legacy_plaintext_but_rejects_invalid_encrypted_values(): void
    {
        $codec = app(MailchimpCredentialCodec::class);

        $this->assertSame('legacy-key-us6', $codec->decode(' legacy-key-us6 '));
        $this->assertSame('', $codec->decode(MailchimpCredentialCodec::PREFIX.'invalid-payload'));
    }
}

<?php

namespace App\Services\Newsletter;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

final class MailchimpCredentialCodec
{
    public const PREFIX = 'enc:v1:';

    public function encode(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        if ($this->isEncrypted($value)) {
            return $value;
        }

        return self::PREFIX.Crypt::encryptString($value);
    }

    public function decode(string $value): string
    {
        $value = trim($value);

        if ($value === '' || ! $this->isEncrypted($value)) {
            return $value;
        }

        try {
            return trim(Crypt::decryptString(substr($value, strlen(self::PREFIX))));
        } catch (DecryptException) {
            return '';
        }
    }

    public function isEncrypted(string $value): bool
    {
        return str_starts_with(trim($value), self::PREFIX);
    }
}

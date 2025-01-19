<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;

class EncryptionHelper
{
    /**
     * Encrypt data into a base64 string.
     *
     * @param mixed $data
     * @return string
     */
    public static function encrypt($data)
    {
        return base64_encode(Crypt::encryptString(json_encode($data)));
    }

    /**
     * Decrypt data from a base64 string.
     *
     * @param string $encryptedData
     * @return mixed
     */
    public static function decrypt($encryptedData)
    {
        return json_decode(Crypt::decryptString(base64_decode($encryptedData)), true);
    }
}
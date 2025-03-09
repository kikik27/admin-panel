<?php

namespace App\Helpers;

class EncryptionHelper
{
    private static $key = "12345678901234567890123456789012"; // 32 karakter

    /**
     * Encrypt data using AES-256-GCM.
     *
     * @param mixed $data
     * @return string
     */
    public static function encrypt($data)
    {
        $key = base64_decode(env('APP_KEY')); // Menggunakan APP_KEY yang valid (32 byte)
        $cipher = 'AES-256-CBC';
        $iv = random_bytes(openssl_cipher_iv_length($cipher));

        $encrypted = openssl_encrypt(json_encode($data), $cipher, $key, 0, $iv);
        $encryptedData = base64_encode($iv . $encrypted); // Gabungkan IV + Ciphertext

        return str_replace(['+', '/', '='], ['-', '_', ''], $encryptedData); // Base64 URL Safe
    }


    /**
     * Decrypt data using AES-256-GCM.
     *
     * @param string $encryptedData
     * @return mixed
     */
    public static function decrypt($encryptedData)
    {
        $key = base64_decode(env('APP_KEY')); // Harus 32 byte
        $cipher = 'AES-256-CBC';

        $data = base64_decode(str_replace(['-', '_'], ['+', '/'], $encryptedData)); // Decode Base64 URL Safe
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = substr($data, 0, $ivLength);
        $ciphertext = substr($data, $ivLength);

        return json_decode(openssl_decrypt($ciphertext, $cipher, $key, 0, $iv));
    }

}

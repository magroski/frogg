<?php

namespace Frogg\Crypto;

/**
 * @deprecated Use magroski/wt
 */
class WT
{

    private static string $encrypt_method = ENCRYPTION_TYPE;
    private static string $iv             = WT_IV;

    /**
     * @param mixed       $object
     */
    public static function encode($object, string $key = WT_KEY) : string
    {
        $json = json_encode($object);

        $key = hash('sha256', $key);

        $ivprefix = substr(base64_encode(hash('sha256', $json . time())), 0, 8);
        $iv       = $ivprefix . self::$iv;

        $output = openssl_encrypt($json, self::$encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);

        return $ivprefix . $output;
    }

    /**
     * @return mixed
     */
    public static function decode(string $token, string $key = WT_KEY)
    {
        $ivprefix = substr($token, 0, 8);
        $token    = substr($token, 8);

        $key = hash('sha256', $key);
        $iv  = $ivprefix . self::$iv;

        return json_decode(openssl_decrypt(base64_decode($token), self::$encrypt_method, $key, 0, $iv));
    }

}

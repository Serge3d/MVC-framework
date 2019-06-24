<?php

namespace app\lib;

class RandomToken {
            
    public static function create($length = 32) {
        if(!isset($length) || intval($length) <= 8 ){
            $length = 32;
        }
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes($length));
        } elseif (function_exists('mcrypt_create_iv')) {
            return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes($length));
        }
    }

    // Соль
    private function Salt(){
        return substr(strtr(base64_encode(hex2bin(RandomToken(32))), '+', '.'), 0, 44);
    }
}
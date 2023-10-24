<?php

namespace App\Service;


class JWTService
{

    public function getPayload(string $token) : array 
    {
        $array = explode('.', $token);
        
        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
    }

}
<?php


namespace App\ValueObjects;


class Responses
{
    private const OK = 'ok';

    public static function getOkResponse(){
        return self::OK;
    }
}
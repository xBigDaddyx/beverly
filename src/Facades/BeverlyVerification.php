<?php

namespace Xbigdaddyx\Beverly\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Xbigdaddyx\Beverly\Beverly
 */
class BeverlyVerification extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'BeverlyVerificationRepository';
        // return \Xbigdaddyx\Beverly\Beverly::class;
    }
}

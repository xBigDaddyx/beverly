<?php

namespace Xbigdaddyx\Beverly\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Xbigdaddyx\Beverly\Beverly
 */
class BeverlySearch extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'BeverlySearchRepository';
        // return \Xbigdaddyx\Beverly\Beverly::class;
    }
}

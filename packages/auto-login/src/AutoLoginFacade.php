<?php

namespace Anwar\AutoLogin;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Anwar\AutoLogin\
 */
class AutoLoginFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auto-login';
    }
}

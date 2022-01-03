<?php

namespace Anwar\ConfigEditor;

class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return new ConfigEditor();
    }
}

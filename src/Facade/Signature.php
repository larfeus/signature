<?php

namespace Larfeus\Signature\Facade;

use Illuminate\Support\Facades\Facade;

class Signature extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Larfeus\Signature\SignatureManager::class;
    }
}

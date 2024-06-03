<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FileUploadSystem extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'disk-system';
    }
}

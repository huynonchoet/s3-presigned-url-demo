<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\FileUploadSystem;

class FileSystemServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->singleton('disk-system', function () {
            return new FileUploadSystem();
        });
    }
}

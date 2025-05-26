<?php

namespace Modules\Rapat\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Rapat\Console\MigrationRapat;

class ConsoleRapatServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    protected $commands = [
        MigrationRapat::class,
    ];
    public function register()
    {
        $this->commands($this->commands);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}

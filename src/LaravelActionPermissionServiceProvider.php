<?php

namespace RobinVanDijk\LaravelActionPermission;

use Illuminate\Support\ServiceProvider;
use RobinVanDijk\LaravelActionPermission\Console\Commands\SyncControllerActions;

class LaravelActionPermissionServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publish();

        $this->registerCommands();
    }

    protected function publish()
    {
        $this->publishes([
            __DIR__ . '/config/action-permission.php' => config_path('action-permission.php'),
        ]);
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncControllerActions::class,
            ]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/action-permission.php',
            'action-permission'
        );

        $this->registerBindings();
    }

    protected function registerBindings()
    {
        $this->app->bind(
            'RobinVanDijk\LaravelActionPermission\Contracts\RouterManagerContract',
            RouterManager::class
        );

        $this->app->bind(
            'RobinVanDijk\LaravelActionPermission\Contracts\ActionManagerContract',
            ActionManager::class
        );
    }
}

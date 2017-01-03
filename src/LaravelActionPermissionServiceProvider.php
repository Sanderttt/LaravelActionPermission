<?php

namespace RobinVanDijk\LaravelActionPermission;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use RobinVanDijk\LaravelActionPermission\Console\Commands\SyncControllerActions;
use RobinVanDijk\LaravelActionPermission\Events\ClearPermissionCacheEvent;
use RobinVanDijk\LaravelActionPermission\Listeners\ClearPermissionCacheListener;

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

        $this->registerEventListeners();

        $this->registerMigrations();
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

    protected function registerEventListeners()
    {
        Event::listen(ClearPermissionCacheEvent::class, ClearPermissionCacheListener::class);
    }

    protected function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
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

<?php

namespace RobinVanDijk\LaravelActionPermission\Console\Commands;

use Illuminate\Console\Command;
use RobinVanDijk\LaravelActionPermission\ActionManager;
use RobinVanDijk\LaravelActionPermission\Contracts\ActionManagerContract;
use RobinVanDijk\LaravelActionPermission\Contracts\RouterManagerContract;
use RobinVanDijk\LaravelActionPermission\RouterManager;

class SyncControllerActions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'action-permission:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync controller actions with database actions.';

    /**
     * Router manager which interacts with laravel router contract.
     *
     * @var RouterManager
     */
    protected $router;

    /**
     * Action manager.
     *
     * @var ActionManager
     */
    protected $action;
    protected $dispatcher;
    protected $config;

    /**
     * Create a new command instance.
     * @param RouterManagerContract $router
     * @param ActionManagerContract $action
     */
    public function __construct(
        RouterManagerContract $router,
        ActionManagerContract $action
    ) {
        parent::__construct();

        $this->router = $router;
        $this->action = $action;
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $routes = $this->router->getRoutes();
        $actions = $this->router->getActionsFromRoutes($routes);
        $this->action->massSync($actions);
        cache()->tags(config('action-permission.cache_key'))->flush();
        $this->info('Done');
    }
}

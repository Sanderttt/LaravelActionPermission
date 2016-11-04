<?php
/**
 * Created by PhpStorm.
 * User: Win10H64
 * Date: 4-11-2016
 * Time: 11:17
 */

namespace RobinVanDijk\LaravelActionPermission\Console\Commands;

use Illuminate\Console\Command;
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
     * Create a new command instance.
     * @param RouterManagerContract $router
     */
    public function __construct(RouterManagerContract $router)
    {
        parent::__construct();

        $this->router = $router;
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $actions = $this->router->getActions();
    }
}

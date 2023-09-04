<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;


class UpdateRoutePermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:update-permission-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update permission routes.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $routes = Route::getRoutes()->getRoutes();

        foreach ($routes as $route) {
            if ($route->getName() != '' && $route->getAction()['middleware']['0'] == 'web') {
                $routeName = $route->getName();
                $parts = explode('.', $routeName);
                $subModel = $parts[0];
                $action = isset($parts[1]) ? $parts[1] : $parts[0];
                $groupName = $route->getAction()['groupName'] ?? null;

                // Use the full route name as 'name'
                $name = $routeName;

                if (count($parts) > 1) {
                    // Use the first part as 'sub-model'
                    $subModel = $parts[0];
                    // Reconstruct the full route name without the first part
                }
                
                // Find existing permission by name
                $permission = Permission::where('name', $routeName)->first();

                if (!is_null($permission)) {
                    // Update existing permission
                    $permission->update([
                        'name' => $name,
                        'module' => $groupName ?? 'Other',
                        'submodule' => $subModel ?? '',
                        'action' => $action ?? $subModel
                    ]);
                } else {
                    // Create new permission
                    Permission::create([
                        'name' => $name,
                        'module' => $groupName ?? 'Other',
                        'submodule' => $subModel ?? '',
                        'action' => $action ?? $subModel
                    ]);
                }
            }
        }

        $this->info('Permission routes updated successfully.');
    }
}

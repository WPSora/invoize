<?php

namespace Invoize\Api\Misc;

use Invoize\Api\Api;
use Invoize\InvoizePlugin;

class MiscAPI extends Api
{
    protected string $routeName = 'misc';

    public function updateDatabase()
    {
        try {
            $plugin = InvoizePlugin::getInstance();
            $plugin->updateDatabase();

            return $this->response([
                'message' => "Database successfully updated"
            ]);
        } catch (\Exception $e) {
            return $this->response([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function registerRoutes()
    {
        $this->registerGetRequest('update-database', [
            'callback' => [$this, 'updateDatabase'],
            'permission_callback' => fn() => true
        ]);
    }
}

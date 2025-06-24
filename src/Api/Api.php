<?php

namespace Invoize\Api;

use Invoize\Classes\Plugin;
use Invoize\Traits\InteractsWithPlugin;

abstract class Api extends \WP_REST_Controller
{
    use InteractsWithPlugin;

    protected string $routeName;

    protected string $routeNamespace;

    public function __construct()
    {
        $this->plugin = Plugin::getInstance();
        $this->routeNamespace = vsprintf('%s/api/%s', [
            $this->plugin->getSlug(),
            $this->getRouteName()
        ]);
    }

    abstract public function registerRoutes();

    public function registerRequest(string $name, array $options)
    {
        register_rest_route($this->routeNamespace, $name, $options);
    }

    public function registerPostRequest($name, $options)
    {
        $this->registerRequest($name, [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => $options['callback'],
            'args' => $options['args'] ?? [],
            'permission_callback' => $options['permission_callback'] ?? [$this, 'defaultPermissionCallback']
        ]);
    }

    public function registerGetRequest($name, $options)
    {
        $this->registerRequest($name, [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => $options['callback'],
            'args' => $options['args'] ?? [],
            'permission_callback' => $options['permission_callback'] ?? [$this, 'defaultPermissionCallback']
        ]);
    }

    public function defaultPermissionCallback()
    {
        return invoizeCheckPermissionIsAllowed();
    }

    public function getRouteName()
    {
        return $this->routeName;
    }

    public function getRouteNamespace()
    {
        return $this->routeNamespace;
    }

    public function response(array $data, int $status = 200)
    {
        return new \WP_REST_Response($data, $status);
    }
}

<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Spiffy\Framework\ApplicationPackage;
use Symfony\Component\HttpFoundation\Response;

class RoutePlugin implements Plugin
{
    /**
     * {@inheritDoc}
     */
    final public function plug(Manager $events)
    {
        $events->on(Application::EVENT_ROUTE, [$this, 'injectRoutes'], 1000);
        $events->on(Application::EVENT_ROUTE, [$this, 'route']);
        $events->on(Application::EVENT_ROUTE_ERROR, [$this, 'route404']);
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     */
    final public function injectRoutes(ApplicationEvent $e)
    {
        $app = $e->getApplication();
        $i = $app->getInjector();

        $pm = $e->getApplication()->getPackageManager();
        $routes = $pm->getMergedConfig()['framework']['routes'];

        /** @var \Spiffy\Route\Router $router */
        $router = $i->nvoke('Router');
        foreach ($routes as $name => $spec) {
            $options = ['defaults' => ['action' => $spec[1]]];

            if (isset($spec[2]) && is_array($spec[2])) {
                $options = array_merge_recursive($spec[2], $options);
            }

            $router->add($name, $spec[0], $options);
        }
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     */
    final public function route(ApplicationEvent $e)
    {
        $app = $e->getApplication();
        $i = $app->getInjector();

        /** @var \Symfony\Component\HttpFoundation\Request $request */
        $request = $app->getRequest();

        /** @var \Spiffy\Route\Router $router */
        $router = $i->nvoke('Router');

        $match = $router->match($request->getRequestUri(), $request->server->all());
        if (null === $match) {
            $e->setType(Application::EVENT_ROUTE_ERROR);
            $app->events()->fire($e);

            return;
        }

        $e->setRouteMatch($match);
        $e->setAction($match->get('action'));
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     */
    final public function route404(ApplicationEvent $e)
    {
        $app = $e->getApplication();
        $response = $app->getResponse();
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
    }
}

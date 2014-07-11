<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Framework\Action\InvalidRouteAction;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;

final class RoutePlugin implements Plugin
{
    /**
     * {@inheritDoc}
     */
    public function plug(Manager $events)
    {
        $events->on(Application::EVENT_ROUTE, [$this, 'injectRoutesAndActions'], 1000);
        $events->on(Application::EVENT_ROUTE, [$this, 'route']);

        $events->on(Application::EVENT_ROUTE_ERROR, [$this, 'handleInvalidRoute']);
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     * @throws Exception\MissingActionException
     */
    public function injectRoutesAndActions(ApplicationEvent $e)
    {
        $app = $e->getApplication();
        $i = $app->getInjector();

        $pm = $e->getApplication()->getPackageManager();
        $routes = $pm->getMergedConfig()['framework']['routes'];

        /** @var \Spiffy\Route\Router $router */
        $router = $i->nvoke('Router');

        /** @var \Spiffy\Dispatch\Dispatcher $d */
        $d = $i->nvoke('Dispatcher');

        foreach ($routes as $name => $spec) {
            if (!isset($spec[1])) {
                throw new Exception\MissingActionException(sprintf(
                    'No action was given for route "%s"',
                    $name
                ));
            }

            $action = $spec[1];
            $options = ['defaults' => ['action' => $action]];

            $d->add($action, function () use ($i, $d, $action) {
                if (is_string($action) && $i->has($action)) {
                    return $i->nvoke($action);
                }
                return $action;
            });

            if (isset($spec[2]) && is_array($spec[2])) {
                $options = array_merge_recursive($spec[2], $options);
            }

            $router->add($name, $spec[0], $options);
        }
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     */
    public function route(ApplicationEvent $e)
    {
        $app = $e->getApplication();
        $i = $app->getInjector();

        /** @var \Symfony\Component\HttpFoundation\Request $request */
        $request = $app->getRequest();

        /** @var \Spiffy\Route\Router $router */
        $router = $i->nvoke('Router');
        $server = $request->server->all();

        $match = $router->match($request->getRequestUri(), $server);
        if (null === $match) {
            $e->setError(Application::ERROR_ROUTE_INVALID);
            $e->setType(Application::EVENT_ROUTE_ERROR);
            $e->setTarget($server);
            $app->events()->fire($e);

            return;
        }

        $e->setRouteMatch($match);
        $e->setAction($match->get('action'));
    }

    /**
     * @param ApplicationEvent $e
     * @return null|\Spiffy\View\ViewModel
     */
    public function handleInvalidRoute(ApplicationEvent $e)
    {
        if ($e->getError() !== Application::ERROR_ROUTE_INVALID) {
            return null;
        }

        $i = $e->getApplication()->getInjector();
        $action = new InvalidRouteAction($i->nvoke('ViewManager'), $i->nvoke('Request'));

        $response = $e->getResponse();
        $response->setStatusCode(404);

        $result = $action($e->getTarget());

        $e->setModel($result);
        $e->setDispatchResult($result);
    }
}

<?php

namespace Spiffy\Framework\Plugin;

use Exception;
use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Spiffy\Framework\ApplicationPackage;
use Spiffy\Route\RouteMatch;
use Spiffy\View\Model;
use Spiffy\View\ViewModel;
use Symfony\Component\HttpFoundation\Response;

class DispatchPlugin implements Plugin
{
    /**
     * {@inheritDoc}
     */
    final public function plug(Manager $events)
    {
        $events->on(Application::EVENT_DISPATCH, [$this, 'injectActions'], 1000);
        $events->on(Application::EVENT_DISPATCH, [$this, 'dispatch']);
        $events->on(Application::EVENT_DISPATCH, [$this, 'createModelFromArray'], -90);
        $events->on(Application::EVENT_DISPATCH, [$this, 'createModelFromNull'], -90);
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     */
    final public function injectActions(ApplicationEvent $e)
    {
        $app = $e->getApplication();
        $i = $app->getInjector();

        $pm = $app->getPackageManager();
        $actions = $pm->getMergedConfig()['framework']['actions'];

        /** @var \Spiffy\Dispatch\Dispatcher $dispatcher */
        $dispatcher = $i->nvoke('Dispatcher');
        foreach ($actions as $name => $spec) {
            $dispatcher->add($name, $spec);
        }
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     */
    final public function dispatch(ApplicationEvent $e)
    {
        $match = $e->getRouteMatch();
        if (!$match instanceof RouteMatch) {
            $this->finish($e, $this->routeNotFound($e));
            return;
        }

        $app = $e->getApplication();
        $i = $app->getInjector();
        $action = $match->get('action');

        /** @var \Spiffy\Dispatch\Dispatcher $dispatcher */
        $d = $i->nvoke('Dispatcher');

        if (!$d->has($action)) {
            $this->finish($e, $this->invalidAction($e));
            return;
        }

        try {
            $this->finish($e, $d->ispatch($action, $match->getParams()));
            return;
        } catch (\Exception $ex) {
            $this->finish($e, $this->actionException($ex, $e));
            return;
        }
    }

    /**
     * @param ApplicationEvent $e
     */
    final public function createModelFromArray(ApplicationEvent $e)
    {
        $result = $e->getDispatchResult();
        if (!is_array($result) || $e->getError()) {
            return;
        }
        $e->setModel(new ViewModel($result));
    }

    /**
     * @param ApplicationEvent $e
     */
    final public function createModelFromNull(ApplicationEvent $e)
    {
        $result = $e->getDispatchResult();
        if (null !== $result || $e->getError()) {
            return;
        }
        $e->setModel(new ViewModel());
    }

    /**
     * @param Exception $ex
     * @param ApplicationEvent $e
     * @return mixed|null
     */
    private function actionException(Exception $ex, ApplicationEvent $e)
    {
        $e->setError(Application::ERROR_DISPATCH_EXCEPTION);
        $e->setType(Application::EVENT_DISPATCH_ERROR);
        $e->set('exception', $ex);

        $result = $e->getApplication()->events()->fire($e);

        if ($result->count() == 0) {
            return null;
        }

        return $result->top();
    }

    /**
     * @param ApplicationEvent $e
     * @return mixed|null
     */
    private function invalidAction(ApplicationEvent $e)
    {
        $e->setError(Application::ERROR_DISPATCH_INVALID);
        $e->setType(Application::EVENT_DISPATCH_ERROR);
        $result = $e->getApplication()->events()->fire($e);

        if ($result->count() == 0) {
            return null;
        }

        return $result->top();
    }

    /**
     * @param ApplicationEvent $e
     * @return mixed
     */
    private function routeNotFound(ApplicationEvent $e)
    {
        $e->setError(Application::ERROR_ROUTE_INVALID);
        $e->setType(Application::EVENT_ROUTE_ERROR);
        $result = $e->getApplication()->events()->fire($e);

        if ($result->count() == 0) {
            return null;
        }

        return $result->top();
    }

    /**
     * @param ApplicationEvent $e
     * @param mixed $result
     */
    private function finish(ApplicationEvent $e, $result)
    {
        $e->setDispatchResult($result);

        if ($result instanceof Response) {
            $e->setResponse($result);
        } else if ($result instanceof Model) {
            $e->setModel($result);
        }
    }
}

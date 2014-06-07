<?php

namespace Spiffy\Framework\Plugin;

use Exception;
use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Spiffy\Route\Route;
use Spiffy\Route\RouteMatch;
use Spiffy\View\Model;
use Spiffy\View\ViewModel;
use Symfony\Component\HttpFoundation\Response;

final class DispatchPlugin implements Plugin
{
    /**
     * {@inheritDoc}
     */
    public function plug(Manager $events)
    {
        $events->on(Application::EVENT_DISPATCH, [$this, 'injectActions'], 1000);
        $events->on(Application::EVENT_DISPATCH, [$this, 'dispatch']);
        $events->on(Application::EVENT_DISPATCH, [$this, 'createModelFromArray'], -90);
        $events->on(Application::EVENT_DISPATCH, [$this, 'createModelFromNull'], -90);
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     */
    public function injectActions(ApplicationEvent $e)
    {
        $app = $e->getApplication();
        $i = $app->getInjector();

        /** @var \Spiffy\Dispatch\Dispatcher $dispatcher */
        $dispatcher = $i->nvoke('Dispatcher');
        foreach ($i['framework']['actions'] as $name => $spec) {
            $dispatcher->add($name, $spec);
        }
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     */
    public function dispatch(ApplicationEvent $e)
    {
        $match = $e->getRouteMatch();
        if (!$match instanceof RouteMatch) {
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
            $match->set('__dispatcher', $d);
            $match->set('__event', $e);

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
    public function createModelFromArray(ApplicationEvent $e)
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
    public function createModelFromNull(ApplicationEvent $e)
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
     * @param mixed $result
     */
    private function finish(ApplicationEvent $e, $result)
    {
        $e->setDispatchResult($result);

        if ($result instanceof Response) {
            $e->setResponse($result);
        } elseif ($result instanceof Model) {
            $e->setModel($result);
        }
    }
}

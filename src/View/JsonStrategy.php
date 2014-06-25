<?php

namespace Spiffy\Framework\View;

use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Spiffy\View\JsonModel;
use Spiffy\View\JsonStrategy as BaseJsonStrategy;
use Spiffy\View\ViewStrategy;

final class JsonStrategy implements Plugin, ViewStrategy
{
    /** @var \Spiffy\View\JsonStrategy */
    private $strategy;

    /**
     * @param BaseJsonStrategy $strategy
     */
    public function __construct(BaseJsonStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * {@inheritDoc}
     */
    public function canRender($nameOrModel)
    {
        return $this->strategy->canRender($nameOrModel);
    }

    /**
     * {@inheritDoc}
     */
    public function render($nameOrModel, array $variables = [])
    {
        return $this->strategy->render($nameOrModel, $variables);
    }

    /**
     * {@inheritDoc}
     */
    public function plug(Manager $events)
    {
        $events->on(Application::EVENT_RESPOND, [$this, 'handleContentType']);
    }

    /**
     * @param ApplicationEvent $e
     */
    public function handleContentType(ApplicationEvent $e)
    {
        if (!$e->getModel() instanceof JsonModel) {
            return;
        }

        $response = $e->getResponse();
        $response->headers->set('Content-Type', 'application/json');
    }
}

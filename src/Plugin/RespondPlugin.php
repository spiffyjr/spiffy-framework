<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;

final class RespondPlugin implements Plugin
{
    /**
     * {@inheritDoc}
     */
    public function plug(Manager $events)
    {
        $events->on(Application::EVENT_RESPOND, [$this, 'respond'], -1000);
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function respond(ApplicationEvent $e)
    {
        $response = $e->getResponse();

        if (!$response->getContent() && $e->getRenderResult()) {
            $response->setContent($e->getRenderResult());
        }
    }
}

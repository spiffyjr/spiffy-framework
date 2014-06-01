<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Symfony\Component\HttpFoundation\Response;

class RespondPlugin implements Plugin
{
    /**
     * {@inheritDoc}
     */
    final public function plug(Manager $events)
    {
        $events->on(Application::EVENT_RESPOND, [$this, 'respond']);
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    final public function respond(ApplicationEvent $e)
    {
        $response = ($e->getResponse() instanceof Response) ? $e->getResponse() : new Response();

        if ($e->getRenderResult()) {
            $response->setContent($e->getRenderResult());
        }
        $response->send();
    }
}

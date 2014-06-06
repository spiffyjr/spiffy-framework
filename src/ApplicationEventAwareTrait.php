<?php

namespace Spiffy\Framework;

trait ApplicationEventAwareTrait
{
    private $applicationEvent;

    /**
     * @param \Spiffy\Framework\ApplicationEvent $applicationEvent
     */
    final public function setApplicationEvent(ApplicationEvent $applicationEvent)
    {
        $this->applicationEvent = $applicationEvent;
    }

    /**
     * @return \Spiffy\Framework\ApplicationEvent
     */
    final public function getApplicationEvent()
    {
        return $this->applicationEvent;
    }
}

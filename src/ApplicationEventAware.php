<?php

namespace Spiffy\Framework;

interface ApplicationEventAware
{
    /**
     * @param \Spiffy\Framework\ApplicationEvent $applicationEvent
     */
    public function setApplicationEvent(ApplicationEvent $applicationEvent);

    /**
     * @return \Spiffy\Framework\ApplicationEvent
     */
    public function getApplicationEvent();
}

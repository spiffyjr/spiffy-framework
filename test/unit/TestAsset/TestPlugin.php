<?php

namespace Spiffy\Framework\TestAsset;

use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Framework\Application;

class TestPlugin implements Plugin
{
    /**
     * @param Manager $events
     * @return void
     */
    public function plug(Manager $events)
    {
        $events->on(Application::EVENT_BOOTSTRAP, function () { return 'bootstrap'; });
        $events->on(Application::EVENT_RENDER, function () { return 'render'; });
        $events->on(Application::EVENT_RESPOND, function () { return 'respond'; });
    }
}

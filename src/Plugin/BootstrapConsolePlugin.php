<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Spiffy\Framework\ApplicationPackage;
use Spiffy\Framework\ConsoleApplication;

final class BootstrapConsolePlugin implements Plugin
{
    /**
     * @var \Spiffy\Framework\ConsoleApplication
     */
    private $console;

    /**
     * @param \Spiffy\Framework\ConsoleApplication $console
     */
    public function __construct(ConsoleApplication $console)
    {
        $this->console = $console;
    }

    /**
     * @param Manager $events
     * @return void
     */
    public function plug(Manager $events)
    {
        $events->on(Application::EVENT_BOOTSTRAP, [$this, 'bootstrapApplicationPackages'], -1000);
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     */
    public function bootstrapApplicationPackages(ApplicationEvent $e)
    {
        $pm = $e->getApplication()->getInjector()->nvoke('PackageManager');

        foreach ($pm->getPackages() as $package) {
            if ($package instanceof ApplicationPackage) {
                $package->bootstrapConsole($this->console);
            }
        }
    }
}

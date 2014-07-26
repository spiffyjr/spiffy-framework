<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Event\EventManager;
use Spiffy\Framework\Application;
use Spiffy\Framework\ConsoleApplication;

/**
 * @coversDefaultClass \Spiffy\Framework\Plugin\BootstrapConsolePlugin
 */
class BootstrapConsolePluginTest extends AbstractPluginTest
{
    /**
     * @var \Spiffy\Framework\Plugin\BootstrapConsolePlugin
     */
    protected $p;

    /**
     * Generator ::__construct
     */
    public function testPlug()
    {
        $p = $this->p;
        $em = new EventManager();
        $p->plug($em);

        $this->assertCount(1, $em->getEvents(Application::EVENT_BOOTSTRAP));
    }

    /**
     * @covers ::bootstrapApplicationPackages
     */
    public function testBootstrapApplicationPackages()
    {
        $p = $this->p;
        $p->bootstrapApplicationPackages($this->event);

        $this->assertTrue($_ENV['console']);
    }

    /**
     * @return \Spiffy\Event\Plugin|BootstrapConsolePlugin
     */
    protected function createPlugin()
    {
        return new BootstrapConsolePlugin(new ConsoleApplication($this->config));
    }
}

<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Event\EventManager;
use Spiffy\Framework\Application;

/**
 * @coversDefaultClass \Spiffy\Framework\Plugin\RenderPlugin
 */
class RenderPluginTest extends AbstractPluginTest
{
    /**
     * @var \Spiffy\Framework\Plugin\RenderPlugin
     */
    protected $p;

    /**
     * @covers ::plug
     */
    public function testPlug()
    {
        $em = new EventManager();

        $p = $this->p;
        $p->plug($em);

        $this->assertCount(1, $em->getEvents(Application::EVENT_RENDER));
    }

    /**
     * @return \Spiffy\Event\Plugin|RenderPlugin
     */
    protected function createPlugin()
    {
        return new RenderPlugin();
    }
}

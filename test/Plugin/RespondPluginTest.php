<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Event\EventManager;
use Spiffy\Framework\Application;

/**
 * @coversDefaultClass \Spiffy\Framework\Plugin\RespondPlugin
 */
class RespondPluginTest extends AbstractPluginTest
{
    /**
     * @var \Spiffy\Framework\Plugin\RespondPlugin
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

        $this->assertCount(1, $em->getEvents(Application::EVENT_RESPOND));
    }

    /**
     * @covers ::respond
     */
    public function testRespond()
    {
        $p = $this->p;
        $event = $this->event;
        
        $response = $event->getResponse();
        $content = $response->getContent();
        
        $p->respond($event);
        $this->assertSame($content, $response->getContent());
        
        $event->setRenderResult('foobar');
        $p->respond($event);
        $this->assertSame($event->getRenderResult(), $response->getContent());
    }

    /**
     * @return \Spiffy\Event\Plugin|RespondPlugin
     */
    protected function createPlugin()
    {
        return new RespondPlugin();
    }
}

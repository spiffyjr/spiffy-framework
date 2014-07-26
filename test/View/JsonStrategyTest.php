<?php

namespace Spiffy\Framework\View;

use Spiffy\Event\EventManager;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Spiffy\View\JsonModel;
use Spiffy\View\JsonStrategy as BaseJsonStrategy;

/**
 * @coversDefaultClass \Spiffy\Framework\View\JsonStrategy
 */
class JsonStrategyTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * @covers ::__construct
     * @covers ::canRender
     * @covers ::render
     */
    public function testProxiesToCompositeStrategy()
    {
        $base = new BaseJsonStrategy();
        $strategy = new JsonStrategy($base);
        
        $model = new JsonModel();
        $this->assertSame($base->canRender($model), $strategy->canRender($model));
        $this->assertSame($base->render($model), $strategy->render($model));
    }

    /**
     * @covers ::plug
     */
    public function testPlug()
    {
        $em = new EventManager();
        $base = new BaseJsonStrategy();
        $strategy = new JsonStrategy($base);
        $strategy->plug($em);
        
        $this->assertCount(1, $em->getEvents(Application::EVENT_RESPOND));
    }
    
    public function testHandleContentType()
    {
        $base = new BaseJsonStrategy();
        $strategy = new JsonStrategy($base);
        $event = new ApplicationEvent(new Application());
        
        // no result for empty view model
        $strategy->handleContentType($event);
        $this->assertCount(2, $event->getResponse()->headers);
        $this->assertNull($event->getResponse()->headers->get('Content-Type'));
        
        $event->setModel(new JsonModel());
        $strategy->handleContentType($event);
        $this->assertSame('application/json', $event->getResponse()->headers->get('Content-Type'));
    }
}
 
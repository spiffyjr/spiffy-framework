<?php

namespace Spiffy\Framework\View;

use Spiffy\Event\EventManager;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Spiffy\Framework\TestAsset\ExceptionViewStrategy;
use Spiffy\Framework\TestAsset\ReturnViewStrategy;
use Spiffy\View\JsonModel;
use Spiffy\View\JsonStrategy as BaseJsonStrategy;
use Spiffy\View\ViewModel;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \Spiffy\Framework\View\ViewManager
 */
class ViewManagerTest extends \PHPUnit_Framework_TestCase 
{
    /** @var ApplicationEvent */
    protected $e;
    /** @var ReturnViewStrategy */
    protected $fallback;
    /** @var ViewManager */
    protected $vm;

    /**
     * @covers ::__construct, ::plug
     */
    public function testPlug()
    {
        $em = new EventManager();
        $vm = $this->vm;
        
        $vm->plug($em);
        $this->assertCount(1, $em->getEvents(Application::EVENT_RENDER));
        $this->assertCount(1, $em->getEvents(Application::EVENT_RESPOND));
    }

    /**
     * @covers ::addStrategy, ::getStrategies
     */
    public function testAddStrategy()
    {
        $vm = $this->vm;
        $this->assertCount(2, $vm->getStrategies());
    }

    /**
     * @covers ::getFallbackStrategy
     */
    public function testGetFallbackStrategy()
    {
        $this->assertSame($this->vm->getFallbackStrategy(), $this->fallback);
    }

    /**
     * @covers ::render
     */
    public function testRenderReturnsIfDispatchResultIsSet()
    {
        $vm = $this->vm;
        $e = $this->e;
        
        $e->setDispatchResult(new Response());
        $this->assertNull($vm->render($e));
    }

    /**
     * @covers ::render
     */
    public function testRenderFallsBackToDefault()
    {
        $vm = $this->vm;
        $e = $this->e;
        
        $e->setModel(new ViewModel());
        
        $vm->render($e);
        $this->assertSame($e->getModel(), $e->getRenderResult());
    }

    /**
     * @covers ::render, ::renderException
     */
    public function testRenderHandlesExceptions()
    {
        $vm = $this->vm;
        $e = $this->e;
        $fired = false;
        $e->getApplication()->events()->on(Application::EVENT_RENDER_ERROR, function() use (&$fired) {
            $fired = true;
        });
        
        $vm->addStrategy(new ExceptionViewStrategy());
        
        $e->setModel(new ViewModel());
        $vm->render($e);
        
        $this->assertTrue($fired);
        $this->assertSame($vm->getErrorTemplate(), $e->getModel()->getTemplate());
    }

    /**
     * @covers ::render
     */
    public function testRender()
    {
        $vm = $this->vm;
        $e = $this->e;
        
        $e->setModel(new JsonModel());
        
        $vm->render($e);
        $this->assertSame(json_encode([]), $e->getRenderResult());
    }

    /**
     * @covers ::getNotFoundTemplate, ::getErrorTemplate, ::setNotFoundTemplate, ::setErrorTemplate
     */
    public function testGetTemplates()
    {
        $vm = $this->vm;
        $this->assertSame('error/404', $vm->getNotFoundTemplate());
        $this->assertSame('error/exception', $vm->getErrorTemplate());
        
        $vm->setNotFoundTemplate('error/not-found');
        $this->assertSame('error/not-found', $vm->getNotFoundTemplate());

        $vm->setErrorTemplate('error/error');
        $this->assertSame('error/error', $vm->getErrorTemplate());
    }
    
    protected function setUp()
    {
        $this->e = new ApplicationEvent(new Application());
        $this->fallback = $fallback = new ReturnViewStrategy();
        $this->vm = new ViewManager($fallback);
        $this->vm->addStrategy(new BaseJsonStrategy());
        $this->vm->addStrategy(new JsonStrategy(new BaseJsonStrategy()));

        $this->e->getApplication()->getInjector()->nject('ViewManager', $this->vm);
    }
}
 
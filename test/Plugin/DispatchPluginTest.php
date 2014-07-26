<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Dispatch\Dispatcher;
use Spiffy\Event\EventManager;
use Spiffy\Framework\Application;
use Spiffy\Framework\View\ViewManager;
use Spiffy\Route\Route;
use Spiffy\Route\RouteMatch;
use Spiffy\Route\Router;
use Spiffy\View\VardumpStrategy;
use Spiffy\View\ViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \Spiffy\Framework\Plugin\DispatchPlugin
 */
class DispatchPluginTest extends AbstractPluginTest
{
    /**
     * @var \Spiffy\Framework\Plugin\DispatchPlugin
     */
    protected $p;

    /**
     * @covers ::plug
     */
    public function testPlug()
    {
        $p = $this->p;
        $em = new EventManager();
        $p->plug($em);

        $this->assertCount(4, $em->getEvents(Application::EVENT_DISPATCH));
    }

    /**
     * @covers ::handleDispatchInvalidResult
     */
    public function testDispatchInvalidResultReturnsWithProperResponse()
    {
        $event = clone $this->event;
        
        $p = $this->p;
        
        $event->setModel(new ViewModel());
        $this->assertNull($p->handleDispatchInvalidResult($event));

        $event = clone $this->event;
        $event->setDispatchResult(new Response());
        $this->assertNull($p->handleDispatchInvalidResult($event));

        $event = clone $this->event;
        $event->setDispatchResult(new ViewModel());
        $this->assertNull($p->handleDispatchInvalidResult($event));
    }

    /**
     * @covers ::handleDispatchInvalidResult
     */
    public function testDispatchInvalidResult()
    {
        $p = $this->p;
        $event = $this->event;
        $event->setDispatchResult('not valid');
     
        $p->handleDispatchInvalidResult($event);
        
        $result = new ViewModel([
            'type' => 'invalid-result',
            'result' => 'not valid'
        ]);
        $result->setTemplate('error/exception');
        
        $this->assertInstanceOf('Spiffy\View\ViewModel', $event->getDispatchResult());
        $this->assertEquals($result, $event->getDispatchResult());
        $this->assertSame(500, $event->getResponse()->getStatusCode());
    }

    /**
     * @covers ::dispatch
     */
    public function testDipatchReturnsIfRouteMatchIsEmpty()
    {
        $p = $this->p;

        $this->assertNull($this->event->getRouteMatch());
        $p->dispatch($this->event);
    }

    /**
     * @covers ::dispatch
     * @covers ::handleDispatchInvalid
     * @covers ::finish
     */
    public function testDispatchWithNoAction()
    {
        $p = $this->p;

        $i = $this->app->getInjector();
        $d = new Dispatcher();

        $i->nject('Dispatcher', $d);

        $match = new RouteMatch(new Route('exception', '/exception'));
        $match->set('action', 'exception');

        $event = $this->event;
        $event->setRouteMatch($match);

        $p->dispatch($event);

        $this->assertTrue($event->hasError());
        $this->assertSame(Application::ERROR_DISPATCH_INVALID, $event->getError());
        $this->assertSame(Application::EVENT_DISPATCH_ERROR, $event->getType());

        $result = new ViewModel([
            'uri' => '/',
            'type' => 'action',
            'action' => 'exception'
        ]);
        $result->setTemplate('error/404');
        
        $p->dispatch($event);
        $this->assertEquals($result, $event->getDispatchResult());
    }


    /**
     * @covers ::dispatch
     * @covers ::handleDispatchException
     * @covers ::finish
     */
    public function testDispatchHandlesActionExceptions()
    {
        $p = $this->p;

        $i = $this->app->getInjector();
        $d = new Dispatcher();
        $d->add('exception', 'Spiffy\Framework\TestAsset\ExceptionAction');

        $i->nject('Dispatcher', $d);

        $match = new RouteMatch(new Route('exception', '/exception'));
        $match->set('action', 'exception');

        $event = $this->event;
        $event->setRouteMatch($match);

        $p->dispatch($event);

        $this->assertTrue($event->hasError());
        $this->assertSame(Application::ERROR_DISPATCH_EXCEPTION, $event->getError());
        $this->assertSame(Application::EVENT_DISPATCH_ERROR, $event->getType());
        $this->assertInstanceOf('RuntimeException', $event->get('exception'));

        $result = new ViewModel([
            'type' => 'exception',
            'exception_class' => 'RuntimeException',
            'exception' => $event->get('exception'),
            'previous_exceptions' => []
        ]);
        $result->setTemplate('error/exception');
        
        $p->dispatch($event);
        $this->assertEquals($result, $event->getDispatchResult());
    }

    /**
     * @covers ::handleDispatchInvalid
     */
    public function testHandleDispatchInvalidWithWrongError()
    {
        $p = $this->p;
        $this->assertNull($p->handleDispatchInvalid($this->event));
    }

    /**
     * @covers ::handleDispatchException
     */
    public function testHandleDispatchExceptionWithWrongError()
    {
        $p = $this->p;
        $this->assertNull($p->handleDispatchException($this->event));
    }

    /**
     * Generator ::finish
     */
    public function testDispatchArrayResult()
    {
        $p = $this->p;
        $event = $this->event;

        $d = new Dispatcher();
        $d->add('test', 'Spiffy\Framework\TestAsset\TestAction');

        $i = $this->app->getInjector();
        $i->nject('Dispatcher', $d);

        // array result
        $match = new RouteMatch(new Route('test', '/test'));
        $match->set('action', 'test');

        $event->setRouteMatch($match);
        $p->dispatch($event);

        $this->assertFalse($event->hasError());
        $this->assertSame(['foo' => 'bar'], $event->getDispatchResult());
    }

    /**
     * Generator ::finish
     */
    public function testDispatchModelResult()
    {
        $p = $this->p;
        $event = $this->event;

        $d = new Dispatcher();
        $d->add('model', 'Spiffy\Framework\TestAsset\ModelAction');

        $i = $this->app->getInjector();
        $i->nject('Dispatcher', $d);

        // array result
        $match = new RouteMatch(new Route('model', '/model'));
        $match->set('action', 'model');

        $event->setRouteMatch($match);
        $p->dispatch($event);

        $this->assertFalse($event->hasError());
        $this->assertInstanceOf('Spiffy\View\ViewModel', $event->getDispatchResult());
        $this->assertSame($event->getDispatchResult(), $event->getModel());
    }

    /**
     * Generator ::finish
     */
    public function testDispatchResponseResult()
    {
        $p = $this->p;
        $event = $this->event;

        $d = new Dispatcher();
        $d->add('response', 'Spiffy\Framework\TestAsset\ResponseAction');

        $i = $this->app->getInjector();
        $i->nject('Dispatcher', $d);

        // array result
        $match = new RouteMatch(new Route('response', '/response'));
        $match->set('action', 'response');

        $event->setRouteMatch($match);
        $p->dispatch($event);

        $this->assertFalse($event->hasError());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $event->getDispatchResult());
        $this->assertSame($event->getDispatchResult(), $event->getResponse());
    }

    /**
     * @covers ::createModelFromArray
     */
    public function testCreateModelFromArray()
    {
        $e = $this->event;
        $p = $this->p;

        $p->createModelFromArray($e);
        $this->assertNull($e->getModel());

        $e->setDispatchResult(['foo' => 'bar']);
        $p->createModelFromArray($e);

        $this->assertInstanceOf('Spiffy\View\ViewModel', $e->getModel());
        $this->assertSame(['foo' => 'bar'], $e->getModel()->getVariables());
    }

    /**
     * @covers ::createModelFromNull
     */
    public function testCreateModelFromNull()
    {
        $e = $this->event;
        $p = $this->p;
        $e->setDispatchResult(['foo' => 'bar']);

        $p->createModelFromNull($e);
        $this->assertNull($e->getModel());

        $e->setDispatchResult(null);
        $p->createModelFromNull($e);

        $this->assertInstanceOf('Spiffy\View\ViewModel', $e->getModel());
    }

    /**
     * @return \Spiffy\Event\Plugin|DispatchPlugin
     */
    protected function createPlugin()
    {
        return new DispatchPlugin();
    }
    
    protected function setUp()
    {
        parent::setUp();
        $i = $this->app->getInjector();
        $i->nject('Request', new Request());
        $i->nject('Router', new Router());
        $i->nject('ViewManager', new ViewManager(new VardumpStrategy()));
    }
}

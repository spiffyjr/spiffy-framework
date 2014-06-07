<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Dispatch\Dispatcher;
use Spiffy\Event\EventManager;
use Spiffy\Framework\Application;
use Spiffy\Route\Route;
use Spiffy\Route\RouteMatch;

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
     * @covers ::injectActions
     */
    public function testInjectActions()
    {
        $p = $this->p;
        $i = $this->app->getInjector();
        $d = new Dispatcher();
        $i->nject('Dispatcher', $d);

        $p->injectActions($this->event);

        $this->assertTrue($d->has('test'));
        $this->assertSame('Spiffy\Framework\TestAsset\TestAction', $d->get('test'));
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
     * @covers ::dispatch, ::invalidAction, ::finish
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

        $this->app->events()->on(Application::EVENT_DISPATCH_ERROR, function ($e) {
            return 'fired';
        });
        $p->dispatch($event);
        $this->assertSame('fired', $event->getDispatchResult());
    }


    /**
     * @covers ::dispatch, ::actionException, ::finish
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

        $this->app->events()->on(Application::EVENT_DISPATCH_ERROR, function ($e) {
            return 'fired';
        });
        $p->dispatch($event);
        $this->assertSame('fired', $event->getDispatchResult());
    }

    /**
     * @covers ::dispatch, ::finish
     */
    public function testDispatch()
    {
        $p = $this->p;
        $event = $this->event;

        $i = $this->app->getInjector();
        $d = new Dispatcher();
        $d->add('test', 'Spiffy\Framework\TestAsset\TestAction');
        $d->add('model', 'Spiffy\Framework\TestAsset\ModelAction');
        $d->add('response', 'Spiffy\Framework\TestAsset\ResponseAction');

        $i->nject('Dispatcher', $d);

        // array result
        $match = new RouteMatch(new Route('test', '/test'));
        $match->set('action', 'test');

        $event->setRouteMatch($match);
        $p->dispatch($event);

        $this->assertFalse($event->hasError());
        $this->assertSame(['foo' => 'bar'], $event->getDispatchResult());

        // model result
        $match = new RouteMatch(new Route('model', '/model'));
        $match->set('action', 'model');

        $event->setRouteMatch($match);
        $p->dispatch($event);

        $this->assertFalse($event->hasError());
        $this->assertInstanceOf('Spiffy\View\ViewModel', $event->getDispatchResult());
        $this->assertSame($event->getDispatchResult(), $event->getModel());

        // response result
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
}

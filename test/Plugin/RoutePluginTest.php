<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Dispatch\Dispatcher;
use Spiffy\Event\EventManager;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Spiffy\Framework\TestAsset\ModelAction;
use Spiffy\Framework\View\ViewManager;
use Spiffy\Package\PackageManager;
use Spiffy\Route\Router;
use Spiffy\View\VardumpStrategy;
use Spiffy\View\ViewModel;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \Spiffy\Framework\Plugin\RoutePlugin
 */
class RoutePluginTest extends AbstractPluginTest
{
    /**
     * @var \Spiffy\Framework\Plugin\RoutePlugin
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

        $this->assertCount(2, $em->getEvents(Application::EVENT_ROUTE));
        $this->assertCount(1, $em->getEvents(Application::EVENT_ROUTE_ERROR));
    }

    /**
     * @covers ::injectRoutesAndActions
     */
    public function testInjectRoutesAndActions()
    {
        $p = $this->p;
        $event = $this->event;
        $pm = $this->app->getPackageManager();
        
        $i = $this->app->getInjector();
        $i->nject('Spiffy\Framework\TestAsset\ModelAction', function($i) {
            return new ModelAction();
        });

        $refl = new \ReflectionClass($pm);
        $config = $refl->getProperty('mergedConfig');
        $config->setAccessible(true);
        $config->setValue($pm, [
            'framework' => [
                'routes' => [
                    'model' => ['/model', 'Spiffy\Framework\TestAsset\ModelAction'],
                    'test' => ['/test', 'Spiffy\Framework\TestAsset\TestAction'],
                    'response' => ['/response', 'Spiffy\Framework\TestAsset\ResponseAction', ['methods' => ['post']]],
                ]
            ]
        ]);
        
        /** @var \Spiffy\Dispatch\Dispatcher $d */
        $d = $i->nvoke('Dispatcher');
        $p->injectRoutesAndActions($event);

        $d->dispatch('Spiffy\Framework\TestAsset\ModelAction', [
            '__dispatcher' => $d,
            '__event' => $event,
            '__router' => $i->nvoke('Router')
        ]);
        $d->dispatch('Spiffy\Framework\TestAsset\TestAction', [
            '__dispatcher' => $d,
            '__event' => $event,
            '__router' => $i->nvoke('Router')
        ]);
        $d->dispatch('Spiffy\Framework\TestAsset\ResponseAction', [
            '__dispatcher' => $d,
            '__event' => $event,
            '__router' => $i->nvoke('Router')
        ]);
    }

    /**
     * @covers ::injectRoutesAndActions
     * @expectedException \Spiffy\Framework\Plugin\Exception\MissingActionException
     * @expectedExceptionMessage No action was given for route "foo"
     */
    public function testInjectRoutesAndActionsThrowsExceptionForMissingAction()
    {
        $p = $this->p;
        $event = $this->event;
        $pm = $this->app->getPackageManager();

        $refl = new \ReflectionClass($pm);
        $config = $refl->getProperty('mergedConfig');
        $config->setAccessible(true);
        $config->setValue($pm, ['framework' => ['routes' => ['foo' => ['/foo'],]]]);

        $p->injectRoutesAndActions($event);
    }

    /**
     * @covers ::Route
     */
    public function testRouteTriggersErrorOnMissingRoute()
    {
        $p = $this->p;
        $event = $this->event;
        $event->setRequest(new Request());
        
        $triggered = false;
        $this->app->events()->on(Application::EVENT_ROUTE_ERROR, function(ApplicationEvent $e) use (&$triggered) {
            $triggered = true;
            $e->stop();
        }, 10000);
        
        $p->route($event);
        
        $this->assertTrue($triggered);
    }

    /**
     * @covers ::Route
     */
    public function testRoute()
    {
        $p = $this->p;
        $event = $this->event;
        $event->setRequest(new Request());

        /** @var \Spiffy\Route\Router $router */
        $router = $this->app->getInjector()->nvoke('Router');
        $router->add('foo', '', ['defaults' => ['action' => 'foobar']]);

        $p->route($event);

        $this->assertInstanceOf('Spiffy\Route\RouteMatch', $event->getRouteMatch());
        $this->assertSame('foobar', $event->getAction());
    }

    /**
     * @covers ::handleInvalidRoute
     */
    public function testDispatchInvalidResult()
    {
        $p = $this->p;
        $event = $this->event;
        
        $this->assertNull($p->handleInvalidRoute($event));
        
        $event->setError(Application::ERROR_ROUTE_INVALID);

        $p->handleInvalidRoute($event);

        $result = new ViewModel([
            'type' => 'route',
            'uri' => '/'
        ]);
        $result->setTemplate('error/404');

        $this->assertInstanceOf('Spiffy\View\ViewModel', $event->getDispatchResult());
        $this->assertEquals($result, $event->getDispatchResult());
        $this->assertSame(404, $event->getResponse()->getStatusCode());
    }

    protected function setUp()
    {
        parent::setUp();
        $i = $this->app->getInjector();
        $i->nject('Dispatcher', new Dispatcher());
        $i->nject('Request', new Request());
        $i->nject('Router', new Router());
        $i->nject('ViewManager', new ViewManager(new VardumpStrategy()));
    }
    
    /**
     * @return \Spiffy\Event\Plugin|RoutePlugin
     */
    protected function createPlugin()
    {
        return new RoutePlugin();
    }
}

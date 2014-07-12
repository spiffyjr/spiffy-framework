<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Event\EventManager;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;

/**
 * @coversDefaultClass \Spiffy\Framework\Plugin\BootstrapPlugin
 */
class BootstrapPluginTest extends AbstractPluginTest
{
    /**
     * @var \Spiffy\Framework\Plugin\BootstrapPlugin
     */
    protected $p;

    /**
     * @covers ::plug
     */
    public function testPlug()
    {
        $p = $this->p;
        $events = new EventManager();

        $p->plug($events);

        $this->assertCount(10, $events->getEvents(Application::EVENT_BOOTSTRAP));
    }

    /**
     * @covers ::injectEnvironment
     */
    public function testInjectEnvironment()
    {
        $p = $this->p;
        $p->injectEnvironment($this->event);

        $this->assertSame('bar', $_ENV['foo']);
    }

    /**
     * @covers ::injectPlugins
     */
    public function testInjectPlugins()
    {
        $p = $this->p;
        $p->injectPlugins($this->event);

        $events = $this->app->events();
        $this->assertCount(11, $events->getEvents(Application::EVENT_BOOTSTRAP));
        $this->assertCount(2, $events->getEvents(Application::EVENT_RENDER));
        $this->assertCount(2, $events->getEvents(Application::EVENT_RESPOND));
    }

    /**
     * @covers ::injectServices
     */
    public function testInjectServices()
    {
        $p = $this->p;
        $p->injectServices($this->event);

        $this->assertInstanceOf('StdClass', $this->app->getInjector()->nvoke('stdclass'));
    }

    /**
     * @covers ::injectApplicationPackageConfigs
     */
    public function testInjectApplicationPackageConfigs()
    {
        $p = $this->p;
        $p->injectApplicationPackageConfigs($this->event);

        $config = include __DIR__ . '/../config/package.php';
        $i = $this->app->getInjector();
        $this->assertSame($config['framework.test-asset'], $i['framework.test-asset']);
    }

    /**
     * @covers ::bootstrapApplicatonPackages
     */
    public function testBootstrapApplicationPackages()
    {
        $p = $this->p;
        $p->bootstrapApplicatonPackages($this->event);

        $this->assertTrue($_ENV['bootstrap']);
    }

    /**
     * @covers ::createViewManager
     * @expectedException \Spiffy\Framework\Plugin\Exception\InvalidFallbackStrategy
     * @expectedExceptionMessage Invalid or missing fallback strategy: object given
     */
    public function testCreateViewManagerThrowsExceptionOnInvalidStrategy()
    {
        $p = $this->p;
        $app = $this->app;
        $i = $app->getInjector();
        $i->nject('Spiffy\View\VardumpStrategy', 'StdClass');

        $p->createViewManager($this->event);
    }

    /**
     * @covers ::createViewManager
     */
    public function testCreateViewManager()
    {
        $p = $this->p;
        $i = $this->app->getInjector();

        $p->createViewManager($this->event);

        $this->assertInstanceOf('Spiffy\Framework\View\ViewManager', $i->nvoke('ViewManager'));
    }

    /**
     * @covers ::createPackageManager
     */
    public function testCreatePackageManager()
    {
        $p = $this->p;
        $app = new Application();
        $event = new ApplicationEvent($app);

        $p->createPackageManager($event);

        $this->assertInstanceOf('Spiffy\Package\PackageManager', $app->getInjector()->nvoke('PackageManager'));
    }

    /**
     * @covers ::createPackageManager
     */
    public function testCreatePackageManagerHandlesDebugPackages()
    {
        $p = $this->p;
        $app = new Application([
            'environment' => [
                'debug' => true
            ],
            'packages' => [
                '?Spiffy\Package\TestAsset\Application'
            ]
        ]);
        $event = new ApplicationEvent($app);

        $p->createPackageManager($event);

        $pm = $app->getInjector()->nvoke('PackageManager');
        $this->assertInstanceOf('Spiffy\Package\PackageManager', $pm);
        $this->assertCount(2, $pm->getPackages());

        $app = new Application(['packages' => ['?Spiffy\Package\TestAsset\Application']]);
        $event = new ApplicationEvent($app);

        $p->createPackageManager($event);

        $pm = $app->getInjector()->nvoke('PackageManager');
        $this->assertInstanceOf('Spiffy\Package\PackageManager', $pm);
        $this->assertCount(1, $pm->getPackages());
    }

    /**
     * @covers ::createRequest
     */
    public function testCreateRequest()
    {
        $p = $this->p;
        $p->createRequest($this->event);

        $request = $this->app->getInjector()->nvoke('Request');
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request);
    }

    /**
     * @covers ::createDispatcher
     */
    public function testCreateDispatcher()
    {
        $p = $this->p;
        $p->createDispatcher($this->event);

        $d = $this->app->getInjector()->nvoke('Dispatcher');
        $this->assertInstanceOf('Spiffy\Dispatch\Dispatcher', $d);
    }

    /**
     * @covers ::createRouter
     */
    public function testCreateRouter()
    {
        $p = $this->p;
        $p->createRouter($this->event);

        $r = $this->app->getInjector()->nvoke('Router');
        $this->assertInstanceOf('Spiffy\Route\Router', $r);
    }

    /**
     * @return \Spiffy\Event\Plugin|BootstrapPlugin
     */
    protected function createPlugin()
    {
        return new BootstrapPlugin();
    }
}

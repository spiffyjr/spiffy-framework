<?php

namespace Spiffy\Framework;
use Spiffy\Dispatch\Dispatcher;
use Spiffy\Package\PackageManager;
use Spiffy\Route\Router;
use Spiffy\View\VardumpStrategy;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \Spiffy\Framework\Application
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Application */
    protected $a;

    /**
     * @covers ::__construct
     * @covers ::isDebug
     */
    public function testIsDebug()
    {
        $a = new Application();
        $this->assertSame($a->isDebug(), $a->getConfig()->isDebug());
    }

    /**
     * @covers ::getEvent
     */
    public function testGetEvent()
    {
        $a = $this->a;
        $this->assertInstanceOf('Spiffy\Framework\ApplicationEvent', $a->getEvent());
        $this->assertSame($a->getEvent()->getApplication(), $a);
    }

    /**
     * @covers ::getConfig
     */
    public function testGetConfig()
    {
        $this->assertInstanceOf('Spiffy\Framework\ApplicationConfig', $this->a->getConfig());
    }

    /**
     * @covers ::getPackageManager
     */
    public function testGetPackageManager()
    {
        $pm = new PackageManager();
        
        $a = $this->a;
        $a->getInjector()->nject('PackageManager', $pm);
        $this->assertSame($pm, $a->getPackageManager());
    }

    /**
     * @covers ::getRequest
     */
    public function testGetRequest()
    {
        $a = $this->a;
        $this->assertSame($a->getRequest(), $a->getEvent()->getRequest());
    }

    /**
     * @covers ::getInjector
     */
    public function testGetInjector()
    {
        $this->assertInstanceOf('Spiffy\Inject\Injector', $this->a->getInjector());
    }

    /**
     * @covers ::events
     * @covers ::attachDefaultPlugins
     */
    public function testAttachingDefaultPlugins()
    {
        $a = $this->a;
        $a->events();
        
        $this->assertCount(8, $a->events()->getEvents());
    }

    /**
     * @covers ::run
     */
    public function testRun()
    {
        $a = $this->a;
        $a->bootstrap();
        
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $a->run());
    }
    
    /**
     * @covers ::getResponse
     */
    public function testGetResponse()
    {
        $a = $this->a;
        $this->assertSame($a->getResponse(), $a->getEvent()->getResponse());
    }

    /**
     * @covers ::bootstrap
     */
    public function testBootstrap()
    {
        $a = $this->a;
        $fired = false;
        $a->events()->on(Application::EVENT_BOOTSTRAP, function () use (&$fired) {
            $fired = true;
        });

        $a->bootstrap();
        $this->assertTrue($fired);
    }
    
    protected function setUp()
    {
        $this->a = new Application();
    }
}
 
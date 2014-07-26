<?php

namespace Spiffy\Framework;

/**
 * @coversDefaultClass \Spiffy\Framework\ApplicationConfig
 */
class ApplicationConfigTest extends \PHPUnit_Framework_TestCase 
{
    /** @var ApplicationConfig */
    protected $c;
    
    /**
     * @covers ::__construct
     * @covers ::getConfigOverrideFlags
     */
    public function testGetConfigOverrideFlags()
    {
        $this->assertSame(0, $this->c->getConfigOverrideFlags());
    }

    /**
     * @covers ::getConfigOverridePattern
     */
    public function testGetConfigOverridePattern()
    {
        $this->assertSame('config/override/*.config.php', $this->c->getConfigOverridePattern());
    }

    /**
     * @covers ::getEnvironment
     */
    public function testGetEnvironment()
    {
        $this->assertSame(['debug' => false], $this->c->getEnvironment());
    }

    /**
     * @covers ::getPackages
     */
    public function testGetPackages()
    {
        $this->assertSame([], $this->c->getPackages());
    }

    /**
     * @covers ::getPlugins
     */
    public function testGetPlugins()
    {
        $this->assertSame([
            'bootstrap' => 'Spiffy\Framework\Plugin\BootstrapPlugin',
            'dispatch' => 'Spiffy\Framework\Plugin\DispatchPlugin',
            'render' => 'Spiffy\Framework\Plugin\RenderPlugin',
            'respond' => 'Spiffy\Framework\Plugin\RespondPlugin',
            'route' => 'Spiffy\Framework\Plugin\RoutePlugin',
        ], $this->c->getPlugins());
    }

    /**
     * @covers ::isDebug
     */
    public function testIsDebug()
    {
        $this->assertSame(false, $this->c->isDebug());
    }
    
    protected function setUp()
    {
        $this->c = new ApplicationConfig();
    }
}
 
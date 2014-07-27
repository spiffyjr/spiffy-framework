<?php

namespace Foo
{
    use Spiffy\Framework\AbstractPackage;

    class Package extends AbstractPackage
    {
        
    }
}

namespace Spiffy\Framework
{
    /**
     * @coversDefaultClass \Spiffy\Framework\AbstractPackage
     */
    class AbstractPackageTest extends \PHPUnit_Framework_TestCase 
    {
        /** @var TestAsset\EmptyPackage\Package */
        protected $p;
    
        /**
         * @covers ::bootstrap
         */
        public function testBootstrap()
        {
            $this->assertNull($this->p->bootstrap(new Application()));
        }
    
        /**
         * @covers ::bootstrapConsole
         */
        public function testBootstrapConsole()
        {
            $console = new ConsoleApplication();
            
            $refl = new \ReflectionClass('Symfony\Component\Console\Application');
            $cmds = $refl->getProperty('commands');
            $cmds->setAccessible(true);
            
            $p = new \Foo\Package();
            $p->bootstrapConsole($console);
            $this->assertCount(2, $cmds->getValue($console));
            
            $p = new TestAsset\TestPackage\Package();
            $p->bootstrapConsole($console);
            
            $this->assertCount(3, $cmds->getValue($console));
        }
    
        /**
         * @covers ::getConfig
         */
        public function testGetConfig()
        {
            $p = new TestAsset\ApplicationPackage\Package();
            $this->assertSame(include __DIR__ . '/TestAsset/ApplicationPackage/config/package.php', $p->getConfig());
            
            $p = $this->p;
            $this->assertSame([], $p->getConfig());
        }
    
        /**
         * @covers ::getRoutes
         */
        public function testGetRoutes()
        {
            $p = new TestAsset\ApplicationPackage\Package();
            $this->assertSame(include __DIR__ . '/TestAsset/ApplicationPackage/config/routes.php', $p->getRoutes());
    
            $p = $this->p;
            $this->assertSame([], $p->getRoutes());
        }
    
        /**
         * @covers ::getServices
         */
        public function testGetServices()
        {
            $p = new TestAsset\ApplicationPackage\Package();
            $this->assertSame(include __DIR__ . '/TestAsset/ApplicationPackage/config/services.php', $p->getServices());
    
            $p = $this->p;
            $this->assertSame([], $p->getServices());
        }
    
        /**
         * @covers ::getNamespace
         */
        public function testGetNamespace()
        {
            $p = $this->p;
            $this->assertSame('Spiffy\Framework\TestAsset\EmptyPackage', $p->getNamespace());
            $this->assertSame('Spiffy\Framework\TestAsset\EmptyPackage', $p->getNamespace());
        }
    
        /**
         * @covers ::getName
         */
        public function testGetName()
        {
            $p = $this->p;
            $this->assertSame('framework.test-asset.empty', $p->getName());
            $this->assertSame('framework.test-asset.empty', $p->getName());
            
            $p = new \Foo\Package();
            $this->assertSame('foo', $p->getName());
        }
        
        /**
         * @covers ::getPath
         */
        public function testGetPath()
        {
            $p = $this->p;
            $this->assertSame(realpath(__DIR__ . '/TestAsset'), $p->getPath());
            $this->assertSame(realpath(__DIR__ . '/TestAsset'), $p->getPath());
        }
        
        protected function setUp()
        {
            $this->p = new TestAsset\EmptyPackage\Package();
        }
    }
}
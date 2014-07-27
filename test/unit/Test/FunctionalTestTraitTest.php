<?php
 
namespace Spiffy\Framework\Test;

/**
 * @coversDefaultClass \Spiffy\Framework\Test\FunctionalTestTrait
 */
class FunctionalTestTraitTest extends \PHPUnit_Framework_TestCase
{
    use FunctionalTestTrait;

    /**
     * @covers ::createClient
     */
    public function testCreateClient()
    {
        $client = $this->createClient();
        
        $this->assertInstanceOf('Spiffy\Framework\Test\TestClient', $client);
        $this->assertSame($client->getApplication(), $this->getApplication());
    }
    
    /**
     * @covers ::getApplication
     */
    public function testGetApplicationWithConfigOverride()
    {
        $app = $this->getApplication(['packages' => ['Spiffy\Framework\TestAsset']]);
        $config = $app->getConfig();
        
        $this->assertCount(1, $config->getPackages());
        $this->assertSame('Spiffy\Framework\TestAsset', $config->getPackages()[0]);
    }

    /**
     * @covers ::getApplication
     * @covers ::reset
     */
    public function testGetApplicationAndReset()
    {
        $app = $this->getApplication();
        
        $this->assertInstanceOf('Spiffy\Framework\Application', $app);
        $this->assertSame($app, $this->getApplication());
        
        $this->reset();
        $this->assertNotSame($app, $this->getApplication());
    }
}

<?php
 
namespace Spiffy\Framework;

use Spiffy\Framework\Test\FunctionalTestTrait;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    use FunctionalTestTrait;
    
    public function testNoRouteMatchByDefault()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        
        $this->assertSame(404, $client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('h1'));
        $this->assertSame('404 route not found', $crawler->filter('h1')->text());
    }
    
    protected function setUp()
    {
        $this->reset();
    }
}

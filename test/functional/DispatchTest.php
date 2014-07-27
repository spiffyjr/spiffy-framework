<?php

namespace Spiffy\Framework;

use Spiffy\Framework\Test\FunctionalTestTrait;

class DispatchTest extends \PHPUnit_Framework_TestCase
{
    use FunctionalTestTrait;

    public function testNoDispatchable()
    {
        $app = $this->getApplication();

        /** @var \Spiffy\Route\Router $router */
        $router = $app->getInjector()->nvoke('Router');
        $router->add('home', '/');

        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertSame(404, $client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('h1'));
        $this->assertSame('404 action not found', $crawler->filter('h1')->text());
        $this->assertContains('The action class <strong></strong> does not exist', $crawler->filter('p')->html());
    }
    
    public function testMissingDispatchable()
    {
        $app = $this->getApplication();

        /** @var \Spiffy\Route\Router $router */
        $router = $app->getInjector()->nvoke('Router');
        $router->add('home', '/', ['defaults' => ['action' => 'foo']]);

        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertSame(404, $client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('h1'));
        $this->assertSame('404 action not found', $crawler->filter('h1')->text());
        $this->assertContains('The action class <strong>foo</strong> does not exist', $crawler->filter('p')->html());
    }

    protected function setUp()
    {
        $this->reset();
    }
}

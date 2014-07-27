<?php

namespace Spiffy\Framework\Test;

use Spiffy\Framework\Application;
use Symfony\Component\BrowserKit\Cookie as DomCookie;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \Spiffy\Framework\Test\TestClient
 */
class TestClientTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * @covers ::setApplication
     * @covers ::getApplication
     */
    public function testGetSetApplication()
    {
        $app = new Application();
        
        $c = new TestClient();
        $c->setApplication($app);
        
        $this->assertSame($app, $c->getApplication());
    }

    /**
     * @covers ::filterResponse
     * @covers ::filterRequest
     * @covers ::doRequest
     */
    public function testRequestWithCookies()
    {
        $response = new Response();
        $cookie = new Cookie('foo', 'bar');
        $response->headers->setCookie($cookie);

        $app = new Application();
        $app->bootstrap();
        $app->getEvent()->setResponse($response);

        $client = new TestClient();
        $client->setApplication($app);

        $crawler = $client->request('get', '/');
        $this->assertInstanceOf('Symfony\Component\DomCrawler\Crawler', $crawler);
        $this->assertSame($response, $client->getResponse());
        
        $this->assertInstanceOf('Symfony\Component\BrowserKit\Response', $client->getInternalResponse());
        
        $headers = $client->getInternalResponse()->getHeaders();
        $this->assertArrayHasKey('Set-Cookie', $headers);
        $this->assertCount(1, $headers['Set-Cookie']);
        
        /** @var DomCookie $cookie */
        $cookie = $headers['Set-Cookie'][0];
        $this->assertSame('foo', $cookie->getName());
        $this->assertSame('bar', $cookie->getValue());
    }

    /**
     * @covers ::filterResponse
     * @covers ::filterRequest
     * @covers ::doRequest
     */
    public function testRequest()
    {
        $response = new Response();
        
        $app = new Application();
        $app->bootstrap();
        $app->getEvent()->setResponse($response);
        
        $client = new TestClient();
        $client->setApplication($app);
        
        $crawler = $client->request('get', '/');
        $this->assertInstanceOf('Symfony\Component\DomCrawler\Crawler', $crawler);
        $this->assertSame($response, $client->getResponse());
    }
}
 
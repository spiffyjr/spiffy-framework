<?php

namespace Spiffy\Framework\Test;

/**
 * @coversDefaultClass \Spiffy\Framework\Test\FeatureContext
 */
class FeatureContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::aRequestToIsMade
     */
    public function testARequestToIsMade()
    {
        $c = new FeatureContext();
        $c->aRequestToIsMade('/foo');

        $this->assertSame('/foo', $c->getClient()->getRequest()->server->get('REQUEST_URI'));
    }

    /**
     * @covers ::theRouteForPathExistsWithAction
     */
    public function testTheRouteForPathExistsWithAction()
    {
        $c = new FeatureContext();
        $c->theRouteForPathExistsWithAction('foo', '/foo', 'foo');

        $app = $c->getApplication();

        /** @var \Spiffy\Route\Router $r */
        $r = $app->getInjector()->nvoke('Router');
        $this->assertNotNull($r->match('/foo'));
    }

    /**
     * @covers ::theActionExists
     */
    public function testTheActionExists()
    {
        $c = new FeatureContext();
        $c->theActionExists('foo');

        $d = $c->getApplication()->getInjector()->nvoke('Dispatcher');
        $this->assertTrue($d->has('foo'));

        $this->assertInstanceOf('Spiffy\View\JsonModel', $d->ispatch('foo'));
    }

    /**
     * @covers ::iShouldGetAResponseCode
     */
    public function testIShouldGetAResponseCode()
    {
        $c = new FeatureContext();
        $c->aRequestToIsMade('/foo');

        $this->assertTrue($c->iShouldGetAResponseCode(404));
    }

    /**
     * @covers ::getClient
     */
    public function testGetClient()
    {
        $c = new FeatureContext();
        $c->aRequestToIsMade('/foo');

        $this->assertInstanceOf('Spiffy\Framework\Test\TestClient', $c->getClient());
    }

    /**
     * @covers ::getCrawler
     */
    public function testGetCrawler()
    {
        $c = new FeatureContext();
        $c->aRequestToIsMade('/foo');

        $this->assertInstanceOf('Symfony\Component\DomCrawler\Crawler', $c->getCrawler());
    }
}
 
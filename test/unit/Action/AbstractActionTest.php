<?php

namespace Spiffy\Framework\Action;

use PHPUnit_Framework_TestCase;
use Spiffy\Dispatch\Dispatcher;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Spiffy\Framework\TestAsset\TestAction;
use Spiffy\Route\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \Spiffy\Framework\Action\AbstractAction
 */
class AbstractActionTest extends PHPUnit_Framework_TestCase
{
    /** @var  Application */
    private $app;
    /** @var  TestAction */
    private $a;
    /** @var  Dispatcher */
    private $d;
    /** @var  ApplicationEvent */
    private $e;
    /** @var  Router */
    private $r;
    
    /**
     * @covers ::getRequest
     */
    public function testGetRequest()
    {
        $e = new ApplicationEvent(new Application());
        $e->setRequest(new Request());

        $a = new TestAction();
        $a->setApplicationEvent($e);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $e->getRequest());
        $this->assertSame($e->getRequest(), $a->getRequest());
    }

    /**
     * @covers ::generateRoute
     */
    public function testGenerateRoute()
    {
        $this->dispatch();
        $this->r->add('foo', '/foo');
        $this->assertSame($this->r->assemble('foo'), $this->a->generateRoute('foo'));
    }

    /**
     * @covers ::redirect
     */
    public function testRedirect()
    {
        $result = $this->a->redirect('foo');
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $result);
        $this->assertSame('foo', $result->getTargetUrl());
        $this->assertSame(302, $result->getStatusCode());

        $result = $this->a->redirect('foo', 301);
        $this->assertSame(301, $result->getStatusCode());
    }

    /**
     * @covers ::isPost
     */
    public function testIsPost()
    {
        $this->dispatch();

        $request = new Request();
        $this->e->setRequest($request);
        
        $request->setMethod('POST');
        $this->assertTrue($this->a->isPost());

        $request->setMethod('GET');
        $this->assertFalse($this->a->isPost());
    }

    /**
     * @covers ::query
     */
    public function testQuery()
    {
        $this->dispatch();

        $request = new Request();
        $this->e->setRequest($request);
        
        $this->assertSame($request->query, $this->a->query());
    }

    /**
     * @covers ::post
     */
    public function testPost()
    {
        $this->dispatch();

        $request = new Request();
        $this->e->setRequest($request);

        $this->assertSame($request->request, $this->a->post());
    }

    /**
     * @covers ::getResponse
     */
    public function testGetResponse()
    {
        $e = new ApplicationEvent(new Application());
        $e->setResponse(new Response());

        $a = new TestAction();
        $a->setApplicationEvent($e);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $e->getResponse());
        $this->assertSame($e->getResponse(), $a->getResponse());
    }

    /**
     * @covers ::dispatch
     * @expectedException \Spiffy\Framework\Action\Exception\DispatchingErrorException
     * @expectedExceptionMessage dispatch() expects __dispatcher, __event and __router params to be available.
     */
    public function testDispatchThrowsExceptionForMissingDispatcher()
    {
        $this->a->dispatch([]);
    }

    /**
     * @covers ::dispatch
     * @expectedException \Spiffy\Framework\Action\Exception\DispatchingErrorException
     * @expectedExceptionMessage dispatch() expects __dispatcher, __event and __router params to be available.
     */
    public function testDispatchThrowsExceptionForMissingEvent()
    {
        $this->a->dispatch(['__dispatcher' => new Dispatcher()]);
    }

    /**
     * @covers ::dispatch
     */
    public function testDispatch()
    {
        $result = $this->dispatch();
        $this->assertSame($this->e, $this->a->getApplicationEvent());
        $this->assertSame(['foo' => 'bar'], $result);
    }
    
    protected function setUp()
    {
        $this->app = new Application();
        $this->a = new TestAction();
        $this->e = new ApplicationEvent($this->app);
        $this->d = new Dispatcher();
        $this->r = new Router();
    }
    
    protected function dispatch()
    {
        return $this->a->dispatch([
            '__dispatcher' => $this->d,
            '__event' => $this->e,
            '__router' => $this->r
        ]);
    }
}

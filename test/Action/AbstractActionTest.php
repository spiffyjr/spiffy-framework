<?php

namespace Spiffy\Framework\Action;

use PHPUnit_Framework_TestCase;
use Spiffy\Dispatch\Dispatcher;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Spiffy\Framework\TestAsset\TestAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \Spiffy\Framework\Action\AbstractAction
 */
class AbstractActionTest extends PHPUnit_Framework_TestCase
{
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
     * @expectedExceptionMessage dispatch() expects __dispatcher and __event params to be available.
     */
    public function testDispatchThrowsExceptionForMissingDispatcher()
    {
        $a = new TestAction();
        $a->dispatch([]);
    }

    /**
     * @covers ::dispatch
     * @expectedException \Spiffy\Framework\Action\Exception\DispatchingErrorException
     * @expectedExceptionMessage dispatch() expects __dispatcher and __event params to be available.
     */
    public function testDispatchThrowsExceptionForMissingEvent()
    {
        $a = new TestAction();
        $a->dispatch(['__dispatcher' => new Dispatcher()]);
    }

    /**
     * @covers ::dispatch
     */
    public function testDispatch()
    {
        $app = new Application();
        $e = new ApplicationEvent($app);
        $d = new Dispatcher();

        $a = new TestAction();
        $result = $a->dispatch(['__dispatcher' => $d, '__event' => $e]);

        $this->assertSame($e, $a->getApplicationEvent());
        $this->assertSame($app->getInjector(), $a->getInjector());
        $this->assertSame(['foo' => 'bar'], $result);
    }
}

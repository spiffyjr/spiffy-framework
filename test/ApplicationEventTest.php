<?php

namespace Spiffy\Framework;

use Spiffy\Route\Route;
use Spiffy\Route\RouteMatch;
use Spiffy\View\ViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @coversDefaultClass \Spiffy\Framework\ApplicationEvent
 */
class ApplicationEventTest extends \PHPUnit_Framework_TestCase 
{
    /** @var Application */
    protected $a;
    /** @var ApplicationEvent */
    protected $e;
    
    /**
     * @covers ::getError, ::setError, ::hasError
     */
    public function testSetGetHasError()
    {
        $e = $this->e;
        $this->assertFalse($e->hasError());
        
        $e->setError('test');
        $this->assertSame('test', $e->getError());
        $this->assertTrue($e->hasError());
    }

    /**
     * @dataProvider provider
     */
    public function testSetterGetter($method, $value)
    {
        $e = $this->e;
        $accessor = 'get' . $method;
        $mutator = 'set' . $method;
        
        $e->{$mutator}($value);
        $this->assertSame($value, $e->{$accessor}());
    }

    /**
     * @covers ::getApplication
     */
    public function testGetApplication()
    {
        $this->assertSame($this->a, $this->e->getApplication());
    }

    /**
     * @covers ::getResponse
     */
    public function testGetResponseIsLazy()
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $this->e->getResponse());
    }
    
    public function provider()
    {
        return [
            ['Action','action'],
            ['Model', new ViewModel()],
            ['Request', new Request()],
            ['Response', new Response()],
            ['RouteMatch', new RouteMatch(new Route('foo', '/foo'))],
            ['DispatchResult', 'dispatch'],
            ['RenderResult', 'render']
        ];
    }
    
    protected function setUp()
    {
        $this->a = new Application();
        $this->e = new ApplicationEvent($this->a);
    }
}
 
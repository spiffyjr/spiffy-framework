<?php
 
namespace Spiffy\Framework\Action;

use Spiffy\Framework\View\ViewManager;
use Spiffy\View\VardumpStrategy;
use Spiffy\View\ViewModel;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \Spiffy\Framework\Action\InvalidRouteAction
 */
class InvalidRouteActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct, ::__invoke
     */
    public function testInvoke()
    {
        $action = new InvalidRouteAction(new ViewManager(new VardumpStrategy()), new Request());
        $result = $action('foo');
        
        $expected = new ViewModel([
            'type' => 'route',
            'uri' => '/'
        ]);
        $expected->setTemplate('error/404');
        
        $this->assertInstanceOf('Spiffy\View\ViewModel', $result);
        $this->assertEquals($expected, $result);
    }
}
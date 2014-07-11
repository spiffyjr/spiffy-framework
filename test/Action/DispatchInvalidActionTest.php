<?php
 
namespace Spiffy\Framework\Action;

use Spiffy\Framework\View\ViewManager;
use Spiffy\View\VardumpStrategy;
use Spiffy\View\ViewModel;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \Spiffy\Framework\Action\DispatchInvalidAction
 */
class DispatchInvalidActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct, ::__invoke
     */
    public function testInvoke()
    {
        $action = new DispatchInvalidAction(new ViewManager(new VardumpStrategy()), new Request());
        $result = $action('foo');
        
        $expected = new ViewModel([
            'uri' => '/',
            'type' => 'action',
            'action' => 'foo'
        ]);
        $expected->setTemplate('error/404');
        
        $this->assertInstanceOf('Spiffy\View\ViewModel', $result);
        $this->assertEquals($expected, $result);
    }
}
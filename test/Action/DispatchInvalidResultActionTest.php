<?php
 
namespace Spiffy\Framework\Action;

use Spiffy\Framework\View\ViewManager;
use Spiffy\View\VardumpStrategy;
use Spiffy\View\ViewModel;

/**
 * @coversDefaultClass \Spiffy\Framework\Action\DispatchInvalidResultAction
 */
class DispatchInvalidResultActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        $action = new DispatchInvalidResultAction(new ViewManager(new VardumpStrategy()));
        $result = $action('foo');
        
        $expected = new ViewModel([
            'type' => 'invalid-result',
            'result' => 'foo'
        ]);
        $expected->setTemplate('error/exception');
        
        $this->assertInstanceOf('Spiffy\View\ViewModel', $result);
        $this->assertEquals($expected, $result);
    }
}
<?php
 
namespace Spiffy\Framework\Action;

use Spiffy\Framework\View\ViewManager;
use Spiffy\View\VardumpStrategy;
use Spiffy\View\ViewModel;

/**
 * @coversDefaultClass \Spiffy\Framework\Action\RenderExceptionAction
 */
class RenderExceptionActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct, ::__invoke
     */
    public function testInvoke()
    {
        $ex2 = new \RuntimeException('second');
        $ex = new \RuntimeException('first', 0, $ex2);
        
        $action = new RenderExceptionAction(new ViewManager(new VardumpStrategy()));
        $result = $action($ex);
        
        $expected = new ViewModel([
            'type' => 'exception',
            'exception_class' => 'RuntimeException',
            'exception' => $ex,
            'previous_exceptions' => [$ex2]
        ]);
        $expected->setTemplate('error/exception');
        
        $this->assertInstanceOf('Spiffy\View\ViewModel', $result);
        $this->assertEquals($expected, $result);
    }
}
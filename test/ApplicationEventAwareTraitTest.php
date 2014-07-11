<?php

namespace Spiffy\Framework;
use Spiffy\Framework\TestAsset\ApplicationEventAware;

/**
 * @coversDefaultClass \Spiffy\Framework\ApplicationEventAwareTrait
 */
class ApplicationEventAwareTraitTest extends \PHPUnit_Framework_TestCase 
{
    public function testGetSetApplicationEvent()
    {
        $event = new ApplicationEvent(new Application());
        
        $class = new ApplicationEventAware();
        $class->setApplicationEvent($event);
        $this->assertSame($event, $class->getApplicationEvent());
    }
}
 
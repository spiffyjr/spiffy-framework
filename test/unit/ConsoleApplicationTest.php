<?php

namespace Spiffy\Framework;

use Spiffy\Framework\TestAsset\InjectorAwareCommand;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * @coversDefaultClass \Spiffy\Framework\ConsoleApplication
 */
class ConsoleApplicationTest extends \PHPUnit_Framework_TestCase 
{
    /** @var ConsoleApplication */
    protected $a;

    /**
     * @covers ::add
     * @covers ::__construct
     * @covers ::getInjector
     * @covers ::getApplication
     */
    public function testAddInjectsInjector()
    {
        $cmd = new InjectorAwareCommand();
        
        $a = $this->a;
        $a->add($cmd);
        
        $this->assertSame($a->getApplication()->getInjector(), $cmd->getInjector());
    }

    /**
     * @cover ::run
     */
    public function testRun()
    {
        $a = $this->a;
        $a->setAutoExit(false);
        
        $input = new ArgvInput([null, '--quiet']);
        $this->assertSame(0, $a->run($input));
    }
    
    protected function setUp()
    {
        $this->a = new ConsoleApplication();
    }
}
 
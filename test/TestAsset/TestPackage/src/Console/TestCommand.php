<?php
 
namespace Spiffy\Framework\TestAsset\TestPackage\Console;

use Symfony\Component\Console\Command\Command;

class TestCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('test:test');
    }
}
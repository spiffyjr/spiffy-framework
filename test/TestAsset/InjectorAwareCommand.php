<?php
 
namespace Spiffy\Framework\TestAsset;

use Spiffy\Inject\InjectorAware;
use Spiffy\Inject\InjectorAwareTrait;
use Symfony\Component\Console\Command\Command;

class InjectorAwareCommand extends Command implements InjectorAware
{
    use InjectorAwareTrait;

    protected function configure()
    {
        $this->setName('test:injector');
    }
}
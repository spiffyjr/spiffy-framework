<?php

namespace Spiffy\Framework;

use Spiffy\Inject\InjectorAware;
use Symfony\Component\Console\Application as BaseConsoleApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleApplication extends BaseConsoleApplication
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->application = new Application($config);

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    public function add(Command $command)
    {
        if ($command instanceof InjectorAware) {
            $command->setInjector($this->getInjector());
        }
        return parent::add($command);
    }

    /**
     * {@inheritDoc}
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->application->bootstrap();
        return parent::run($input, $output);
    }

    /**
     * @return \Spiffy\Inject\Injector
     */
    public function getInjector()
    {
        return $this->getApplication()->getInjector();
    }

    /**
     * @return \Spiffy\Framework\Application
     */
    public function getApplication()
    {
        return $this->application;
    }
}

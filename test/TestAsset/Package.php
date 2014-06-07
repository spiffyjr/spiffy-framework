<?php

namespace Spiffy\Framework\TestAsset;

use Spiffy\Framework\AbstractPackage;
use Spiffy\Framework\Application;
use Spiffy\Framework\ConsoleApplication;

class Package extends AbstractPackage
{
    public function bootstrap(Application $app)
    {
        $_ENV['bootstrap'] = true;
    }

    public function bootstrapConsole(ConsoleApplication $console)
    {
        $_ENV['console'] = true;
    }
}

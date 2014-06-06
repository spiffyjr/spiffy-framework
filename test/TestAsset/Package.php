<?php

namespace Spiffy\Framework\TestAsset;

use Spiffy\Framework\AbstractPackage;
use Spiffy\Framework\Application;

class Package extends AbstractPackage
{
    public function bootstrap(Application $app)
    {
        $_ENV['bootstrap'] = true;
    }
}

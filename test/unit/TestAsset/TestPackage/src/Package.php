<?php
 
namespace Spiffy\Framework\TestAsset\TestPackage;

use Spiffy\Framework\AbstractPackage;

class Package extends AbstractPackage
{
    /**
     * @return bool|void
     */
    public function isAutoloadServicesEnabled()
    {
        return true;
    }
}

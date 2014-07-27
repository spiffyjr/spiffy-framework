<?php
 
namespace Spiffy\Framework\TestAsset\ApplicationPackage;

use Spiffy\Framework\AbstractPackage;

class Package extends AbstractPackage
{
    /**
     * @return string
     */
    public function getPath()
    {
        return __DIR__;
    }

    /**
     * @return bool|void
     */
    public function isAutoloadServicesEnabled()
    {
        return true;
    }
}
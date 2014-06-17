<?php

namespace Spiffy\Framework\Twig;

use Spiffy\Inject\Injector;
use Spiffy\Inject\ServiceFactory;

class TwigLoaderFactory implements ServiceFactory
{
    /**
     * @param Injector $i
     * @return \Twig_Loader_Filesystem
     */
    public function createService(Injector $i)
    {
        return new \Twig_Loader_Filesystem(array_reverse($i['framework']['twig']['paths']));
    }
}

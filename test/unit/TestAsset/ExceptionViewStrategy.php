<?php
 
namespace Spiffy\Framework\TestAsset;

use Spiffy\View\ViewStrategy;

class ExceptionViewStrategy implements ViewStrategy
{
    /**
     * {@inheritDoc}
     */
    public function canRender($nameOrModel)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function render($nameOrModel, array $variables = [])
    {
        throw new \RuntimeException('foobar');
    }
}
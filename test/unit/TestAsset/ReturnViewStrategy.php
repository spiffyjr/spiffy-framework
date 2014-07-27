<?php
 
namespace Spiffy\Framework\TestAsset;

use Spiffy\View\ViewStrategy;

class ReturnViewStrategy implements ViewStrategy
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
        return $nameOrModel;
    }
}
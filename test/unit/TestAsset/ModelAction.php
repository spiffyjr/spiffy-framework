<?php

namespace Spiffy\Framework\TestAsset;

use Spiffy\Framework\Action\AbstractAction;
use Spiffy\View\ViewModel;

class ModelAction extends AbstractAction
{
    /**
     * {@inheritDoc}
     */
    public function __invoke()
    {
        return new ViewModel();
    }
}

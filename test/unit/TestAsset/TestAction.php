<?php

namespace Spiffy\Framework\TestAsset;

use Spiffy\Framework\Action\AbstractAction;

class TestAction extends AbstractAction
{
    /**
     * {@inheritDoc}
     */
    public function __invoke()
    {
        return ['foo' => 'bar'];
    }
}

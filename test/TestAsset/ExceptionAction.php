<?php

namespace Spiffy\Framework\TestAsset;

use Spiffy\Framework\Action\AbstractAction;

class ExceptionAction extends AbstractAction
{
    /**
     * {@inheritDoc}
     */
    public function __invoke()
    {
        throw new \RuntimeException('foobar');
    }
}

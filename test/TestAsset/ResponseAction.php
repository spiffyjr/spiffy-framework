<?php

namespace Spiffy\Framework\TestAsset;

use Spiffy\Framework\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Response;

class ResponseAction extends AbstractAction
{
    /**
     * {@inheritDoc}
     */
    public function __invoke()
    {
        return new Response();
    }
}

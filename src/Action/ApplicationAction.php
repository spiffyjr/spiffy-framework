<?php

namespace Spiffy\Framework\Action;

use Spiffy\Dispatch\Dispatchable;
use Spiffy\Framework\ApplicationEventAware;

interface ApplicationAction extends ApplicationEventAware, Dispatchable
{
}

<?php

namespace Spiffy\Framework\Action;

use Spiffy\Dispatch\Dispatchable;
use Spiffy\Framework\ApplicationEventAware;
use Spiffy\Inject\InjectorAware;

interface ApplicationAction extends ApplicationEventAware, Dispatchable, InjectorAware
{
}

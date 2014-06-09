<?php

namespace Spiffy\Framework\Action;

use Spiffy\Framework\ApplicationEventAwareTrait;
use Spiffy\Inject\InjectorAwareTrait;

abstract class AbstractAction implements ApplicationAction
{
    use ApplicationEventAwareTrait;
    use InjectorAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function dispatch(array $params)
    {
        if (!isset($params['__dispatcher']) || !isset($params['__event'])) {
            throw new Exception\DispatchingErrorException(
                'dispatch() expects __dispatcher and __event params to be available.'
            );
        }

        /** @var \Spiffy\Dispatch\Dispatcher $d */
        $d = $params['__dispatcher'];

        /** @var \Spiffy\Framework\ApplicationEvent $e */
        $e = $params['__event'];

        $app = $e->getApplication();

        $this->setApplicationEvent($e);
        $this->setInjector($app->getInjector());

        return $d->dispatchInvokable($this, $params);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    final public function getRequest()
    {
        return $this->getApplicationEvent()->getRequest();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    final public function getResponse()
    {
        return $this->getApplicationEvent()->getResponse();
    }
}

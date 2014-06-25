<?php

namespace Spiffy\Framework\Action;

use Spiffy\Framework\ApplicationEventAwareTrait;
use Spiffy\Inject\InjectorAwareTrait;

abstract class AbstractAction implements ApplicationAction
{
    use ApplicationEventAwareTrait;

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

        $e->set('__action', get_called_class());

        $this->setApplicationEvent($e);
        return $d->dispatchInvokable($this, $params);
    }

    /**
     * @return boolean
     */
    final public function isPost()
    {
        return $this->getRequest()->getMethod() == 'POST';
    }

    /**
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function query()
    {
        return $this->getRequest()->query;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    final public function post()
    {
        return $this->getRequest()->request;
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

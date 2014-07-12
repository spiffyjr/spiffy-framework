<?php

namespace Spiffy\Framework\Action;

use Spiffy\Framework\ApplicationEventAwareTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class AbstractAction implements ApplicationAction
{
    use ApplicationEventAwareTrait;

    /**
     * Set during dispatching from the __router param.
     *
     * @var \Spiffy\Route\Router
     */
    private $router;

    /**
     * {@inheritDoc}
     */
    final public function dispatch(array $params)
    {
        if (!isset($params['__dispatcher']) || !isset($params['__event']) || !isset($params['__router'])) {
            throw new Exception\DispatchingErrorException(
                'dispatch() expects __dispatcher, __event and __router params to be available.'
            );
        }

        /** @var \Spiffy\Dispatch\Dispatcher $d */
        $d = $params['__dispatcher'];

        /** @var \Spiffy\Framework\ApplicationEvent $e */
        $e = $params['__event'];
        $e->set('__dispatched_class', get_called_class());

        $this->router = $params['__router'];

        $this->setApplicationEvent($e);
        return $d->dispatchInvokable($this, $params);
    }

    /**
     * @param string $name
     * @param array $params
     * @return string
     */
    final public function generateRoute($name, array $params = [])
    {
        return $this->router->assemble($name, $params);
    }

    /**
     * @param string $uri
     * @param int $status
     * @return RedirectResponse
     */
    final public function redirect($uri, $status = 302)
    {
        return new RedirectResponse($uri, $status);
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
    final public function query()
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

<?php

namespace Spiffy\Framework;

use Spiffy\Event\Event;
use Spiffy\Route\RouteMatch;
use Spiffy\View\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationEvent extends Event
{
    /**
     * @var string
     */
    private $action;

    /**
     * @var \Spiffy\Framework\Application
     */
    private $application;

    /**
     * @var string
     */
    private $error;

    /**
     * @var \Spiffy\View\Model
     */
    private $model;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    private $response;

    /**
     * @var mixed
     */
    private $dispatchResult;

    /**
     * @var string
     */
    private $renderResult;

    /**
     * @var \Spiffy\Route\RouteMatch
     */
    private $routeMatch;

    /**
     * @param \Spiffy\Framework\Application $application
     */
    final public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @param string $error
     */
    final public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @return string
     */
    final public function getError()
    {
        return $this->error;
    }

    /**
     * @return bool
     */
    final public function hasError()
    {
        return $this->error !== null;
    }

    /**
     * @param string $action
     */
    final public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    final public function getAction()
    {
        return $this->action;
    }

    /**
     * @param \Spiffy\View\Model $model
     */
    public function setModel(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return \Spiffy\View\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    final public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    final public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    final public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    final public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param \Spiffy\Route\RouteMatch $routeMatch
     */
    final public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
    }

    /**
     * @return \Spiffy\Route\RouteMatch
     */
    final public function getRouteMatch()
    {
        return $this->routeMatch;
    }

    /**
     * @param mixed $dispatchResult
     */
    final public function setDispatchResult($dispatchResult)
    {
        $this->dispatchResult = $dispatchResult;
    }

    /**
     * @return mixed
     */
    final public function getDispatchResult()
    {
        return $this->dispatchResult;
    }

    /**
     * @param string $renderResult
     */
    final public function setRenderResult($renderResult)
    {
        $this->renderResult = $renderResult;
    }

    /**
     * @return string
     */
    final public function getRenderResult()
    {
        return $this->renderResult;
    }

    /**
     * @return \Spiffy\Framework\Application
     */
    final public function getApplication()
    {
        return $this->application;
    }
}

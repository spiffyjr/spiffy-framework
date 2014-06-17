<?php

namespace Spiffy\Framework;

use Spiffy\Event\Event;
use Spiffy\Route\RouteMatch;
use Spiffy\View\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ApplicationEvent extends Event
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
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @param string $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->error !== null;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
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
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        if ($this->response instanceof Response) {
            return $this->response;
        }
        $this->response = new Response();
        return $this->response;
    }

    /**
     * @param \Spiffy\Route\RouteMatch $routeMatch
     */
    public function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
    }

    /**
     * @return \Spiffy\Route\RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->routeMatch;
    }

    /**
     * @param mixed $dispatchResult
     */
    public function setDispatchResult($dispatchResult)
    {
        $this->dispatchResult = $dispatchResult;
    }

    /**
     * @return mixed
     */
    public function getDispatchResult()
    {
        return $this->dispatchResult;
    }

    /**
     * @param string $renderResult
     */
    public function setRenderResult($renderResult)
    {
        $this->renderResult = $renderResult;
    }

    /**
     * @return string
     */
    public function getRenderResult()
    {
        return $this->renderResult;
    }

    /**
     * @return \Spiffy\Framework\Application
     */
    public function getApplication()
    {
        return $this->application;
    }
}

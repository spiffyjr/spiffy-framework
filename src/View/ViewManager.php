<?php

namespace Spiffy\Framework\View;

use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Spiffy\View\ViewStrategy;
use Symfony\Component\HttpFoundation\Response;

class ViewManager implements Plugin
{
    /**
     * @var string
     */
    private $exceptionTemplate = 'error/exception';

    /**
     * @var string
     */
    private $notFoundTemplate = 'error/404';

    /**
     * @var \Spiffy\View\ViewStrategy
     */
    private $fallbackStrategy;

    /**
     * @var \Spiffy\View\ViewStrategy[]
     */
    private $strategies = [];

    /**
     * @param ViewStrategy $fallbackStrategy
     */
    final public function __construct(ViewStrategy $fallbackStrategy)
    {
        $this->fallbackStrategy = $fallbackStrategy;
    }

    /**
     * {@inheritDoc}
     */
    public function plug(Manager $events)
    {
        $events->on(Application::EVENT_RENDER, [$this, 'render'], -1000);
    }

    /**
     * @param ViewStrategy $strategy
     */
    final public function addStrategy(ViewStrategy $strategy)
    {
        $this->strategies[] = $strategy;
    }

    /**
     * @param ApplicationEvent $e
     */
    final public function render(ApplicationEvent $e)
    {
        if ($e->getDispatchResult() instanceof Response) {
            return;
        }

        $result = null;

        foreach ($this->strategies as $strategy) {
            if (!$strategy->canRender($e->getModel())) {
                continue;
            }

            try {
                $result = $strategy->render($e->getModel());
            } catch (\Exception $ex) {
                $this->renderException($ex, $e);
                break;
            }

            if (null !== $result) {
                break;
            }
        }

        if (null === $result) {
            $result = $this->fallbackStrategy->render($e->getModel());
        }

        $e->setRenderResult($result);
    }

    /**
     * @return \Spiffy\View\ViewStrategy
     */
    final public function getFallbackStrategy()
    {
        return $this->fallbackStrategy;
    }

    /**
     * @param string $exceptionTemplate
     */
    final public function setExceptionTemplate($exceptionTemplate)
    {
        $this->exceptionTemplate = $exceptionTemplate;
    }

    /**
     * @return string
     */
    final public function getExceptionTemplate()
    {
        return $this->exceptionTemplate;
    }

    /**
     * @param string $notFoundTemplate
     */
    final public function setNotFoundTemplate($notFoundTemplate)
    {
        $this->notFoundTemplate = $notFoundTemplate;
    }

    /**
     * @return string
     */
    final public function getNotFoundTemplate()
    {
        return $this->notFoundTemplate;
    }

    /**
     * @param \Exception $ex
     * @param ApplicationEvent $e
     */
    private function renderException(\Exception $ex, ApplicationEvent $e)
    {
        $e->setError(Application::ERROR_RENDER_EXCEPTION);
        $e->setType(Application::EVENT_RENDER_ERROR);
        $e->set('exception', $ex);
        $e->getApplication()->events()->fire($e);

        $e->getModel()->setTemplate($this->exceptionTemplate);
    }
}

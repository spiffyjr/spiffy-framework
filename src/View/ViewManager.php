<?php

namespace Spiffy\Framework\View;

use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Spiffy\View\ViewStrategy;
use Symfony\Component\HttpFoundation\Response;

final class ViewManager implements Plugin
{
    /**
     * @var string
     */
    private $errorTemplate = 'error/exception';

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
    public function __construct(ViewStrategy $fallbackStrategy)
    {
        $this->fallbackStrategy = $fallbackStrategy;
    }

    /**
     * {@inheritDoc}
     */
    public function plug(Manager $events)
    {
        $events->on(Application::EVENT_RENDER, [$this, 'render'], -1000);

        foreach ($this->strategies as $strategy) {
            if (!$strategy instanceof Plugin) {
                continue;
            }
            $events->plug($strategy);
        }
    }

    /**
     * @param ViewStrategy $strategy
     */
    public function addStrategy(ViewStrategy $strategy)
    {
        $this->strategies[] = $strategy;
    }

    /**
     * @param ApplicationEvent $e
     */
    public function render(ApplicationEvent $e)
    {
        if ($e->getDispatchResult() instanceof Response) {
            return;
        }

        $result = null;

        foreach ($this->strategies as $strategy) {
            if (!$strategy->canRender($e->getModel())) {
                continue;
            }

            $result = $this->renderWithStrategy($strategy, $e);
            
            // Exception was thrown - break loop and show exception using fallback adapter.
            if (false === $result) {
                break;
            }

            if (null !== $result) {
                break;
            }
        }

        if (null === $result) {
            $result = $this->renderWithStrategy($this->fallbackStrategy, $e);
            
            // Exception was thrown - retry once using fallback strategy.
            if (false === $result) {
                $result = $this->renderWithStrategy($this->fallbackStrategy, $e);
            }
        }
        
        $e->setRenderResult($result);
    }

    /**
     * @return \Spiffy\View\ViewStrategy
     */
    public function getFallbackStrategy()
    {
        return $this->fallbackStrategy;
    }

    /**
     * @param string $errorTemplate
     */
    public function setErrorTemplate($errorTemplate)
    {
        $this->errorTemplate = $errorTemplate;
    }

    /**
     * @return string
     */
    public function getErrorTemplate()
    {
        return $this->errorTemplate;
    }

    /**
     * @param string $notFoundTemplate
     */
    public function setNotFoundTemplate($notFoundTemplate)
    {
        $this->notFoundTemplate = $notFoundTemplate;
    }

    /**
     * @return string
     */
    public function getNotFoundTemplate()
    {
        return $this->notFoundTemplate;
    }

    /**
     * @return \Spiffy\View\ViewStrategy[]
     */
    public function getStrategies()
    {
        return $this->strategies;
    }

    /**
     * @param ViewStrategy $strategy
     * @param ApplicationEvent $e
     * @return bool|string
     */
    private function renderWithStrategy(ViewStrategy $strategy, ApplicationEvent $e)
    {
        try {
            return $strategy->render($e->getModel());
        } catch (\Exception $ex) {
            $this->renderException($ex, $e);
        }
        return false;
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
    }
}

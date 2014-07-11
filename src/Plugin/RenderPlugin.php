<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Framework\Action\RenderExceptionAction;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Spiffy\View\ViewModel;

final class RenderPlugin implements Plugin
{
    /**
     * {@inheritDoc}
     */
    public function plug(Manager $events)
    {
        $events->on(Application::EVENT_RENDER, [$this, 'injectTemplate'], 100);
        $events->on(Application::EVENT_RENDER_ERROR, [$this, 'handleRenderException']);
    }

    /**
     * @param ApplicationEvent $e
     * @return null|ViewModel
     */
    public function handleRenderException(ApplicationEvent $e)
    {
        if ($e->getError() !== Application::ERROR_RENDER_EXCEPTION) {
            return;
        }

        $i = $e->getApplication()->getInjector();
        $action = new RenderExceptionAction($i->nvoke('ViewManager'));

        $response = $e->getResponse();
        $response->setStatusCode(500);

        $model = $action($e->get('exception'));

        $e->setModel($model);
        $e->setDispatchResult($model);
    }

    /**
     * @param \Spiffy\Framework\ApplicationEvent $e
     */
    public function injectTemplate(ApplicationEvent $e)
    {
        $model = $e->getModel();

        if (!$model instanceof ViewModel) {
            return;
        }

        if ($model->getTemplate()) {
            return;
        }

        $action = $e->getAction();
        if (!$action) {
            return;
        }

        $replace = function ($match) {
            return $match[1] . '-' . $match[2];
        };

        $template = preg_replace('@Action$@', '', $action);
        $template = preg_replace_callback('@([a-z])([A-Z])@', $replace, $template);
        $template = strtolower($template);
        $template = str_replace('\\', '/', $template);

        $model->setTemplate($template);
    }
}

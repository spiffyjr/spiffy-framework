<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
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

        $action = $e->get('__action');
        if (!$action) {
            return;
        }

        $template = preg_replace('@Action$@', '', $action);
        $template = strtolower($template);
        $template = str_replace('\\', '/', $template);

        $model->setTemplate($template);
    }
}

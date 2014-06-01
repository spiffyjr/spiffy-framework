<?php

namespace Spiffy\Framework\Plugin;

use Spiffy\Event\Manager;
use Spiffy\Event\Plugin;
use Spiffy\Framework\Application;
use Spiffy\Framework\ApplicationEvent;
use Spiffy\View\Model;

class RenderPlugin implements Plugin
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
    final public function injectTemplate(ApplicationEvent $e)
    {
        $model = $e->getModel();

        if (!$model instanceof Model) {
            return;
        }

        if ($model->getTemplate()) {
            return;
        }

        $action = $e->getAction();
        if (null === $action) {
            return;
        }

        $i = $e->getApplication()->getInjector();

        /** @var \Spiffy\Dispatch\Dispatcher $d */
        $d = $i->nvoke('Dispatcher');

        if (!$d->has($e->getAction())) {
            return;
        }

        $action = $d->get($action);
        if (!is_string($action)) {
            return;
        }

        $template = preg_replace('@Action$@', '', $action);
        $template = strtolower($template);
        $template = str_replace('\\', '/', $template);

        $model->setTemplate($template);
    }
}

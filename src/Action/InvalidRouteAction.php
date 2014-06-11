<?php

namespace Spiffy\Framework\Action;

use Spiffy\Framework\View\ViewManager;
use Spiffy\View\ViewModel;

class InvalidRouteAction extends AbstractAction
{
    /**
     * @var ViewManager
     */
    private $vm;

    /**
     * @param \Spiffy\Framework\View\ViewManager $vm
     */
    public function __construct(ViewManager $vm)
    {
        $this->vm = $vm;
    }

    /**
     * @param array $server
     * @return \Spiffy\View\ViewModel
     */
    public function __invoke($server)
    {
        $model = new ViewModel();
        $model->setTemplate($this->vm->getNotFoundTemplate());
        $model->setVariables([
            'type' => 'route',
            'uri' => $server['REQUEST_URI']
        ]);

        return $model;
    }
}

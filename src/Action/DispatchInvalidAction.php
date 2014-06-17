<?php

namespace Spiffy\Framework\Action;

use Spiffy\Framework\View\ViewManager;
use Spiffy\View\ViewModel;

class DispatchInvalidAction extends AbstractAction
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
     * @param string $action
     * @return \Spiffy\View\ViewModel
     */
    public function __invoke($action)
    {
        $model = new ViewModel();
        $model->setTemplate($this->vm->getNotFoundTemplate());
        $model->setVariables([
            'uri' => $_SERVER['REQUEST_URI'],
            'type' => 'action',
            'action' => $action,
        ]);

        return $model;
    }
}

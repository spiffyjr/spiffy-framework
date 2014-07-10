<?php

namespace Spiffy\Framework\Action;

use Spiffy\Framework\View\ViewManager;
use Spiffy\View\ViewModel;
use Symfony\Component\HttpFoundation\Request;

class InvalidRouteAction extends AbstractAction
{
    /** @var \Symfony\Component\HttpFoundation\Request  */
    private $request;
    /** @var \Spiffy\Framework\View\ViewManager  */
    private $vm;

    /**
     * @param \Spiffy\Framework\View\ViewManager $vm
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(ViewManager $vm, Request $request)
    {
        $this->request = $request;
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
            'uri' => $this->request->getPathInfo(),
            'type' => 'route',
        ]);

        return $model;
    }
}

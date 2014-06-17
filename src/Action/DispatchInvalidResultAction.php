<?php

namespace Spiffy\Framework\Action;

use Spiffy\Framework\View\ViewManager;
use Spiffy\View\ViewModel;

class DispatchInvalidResultAction extends AbstractAction
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
     * @param string $result
     * @return \Spiffy\View\ViewModel
     */
    public function __invoke($result)
    {
        $model = new ViewModel();
        $model->setTemplate($this->vm->getExceptionTemplate());
        $model->setVariables([
            'type' => 'invalid-result',
            'result' => $result,
        ]);

        return $model;
    }
}

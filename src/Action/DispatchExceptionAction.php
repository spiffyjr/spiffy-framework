<?php

namespace Spiffy\Framework\Action;

use Spiffy\Framework\View\ViewManager;
use Spiffy\View\ViewModel;

class DispatchExceptionAction extends AbstractAction
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
     * @param \Exception $exception
     * @return \Spiffy\View\ViewModel
     */
    public function __invoke(\Exception $exception)
    {
        $previous = [];

        $e = $exception;
        while ($e->getPrevious()) {
            $e = $e->getPrevious();
            $previous[] = $e;
        }

        $model = new ViewModel();
        $model->setTemplate($this->vm->getExceptionTemplate());
        $model->setVariables([
            'type' => 'dispatch',
            'exception_class' => get_class($exception),
            'exception' => $exception,
            'previous_exceptions' => $previous
        ]);

        return $model;
    }
}

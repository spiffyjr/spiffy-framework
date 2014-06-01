<?php

return [
    // Twig Configuration
    'twig.environment' => ['Twig_Environment', ['@twig.loader.filesystem', '$framework[twig][options]']],
    'twig.loader.filesystem' => ['Twig_Loader_Filesystem', ['$framework[twig][paths]']],

    'spiffy.view.twig.twig-resolver' => ['Spiffy\View\Twig\TwigResolver', ['@twig.environment']],
    'spiffy.view.twig.twig-renderer' => ['Spiffy\View\Twig\TwigRenderer', ['@twig.environment', '@spiffy.view.twig.twig-resolver']],
    'spiffy.view.twig.twig-strategy' => ['Spiffy\View\Twig\TwigStrategy', ['@spiffy.view.twig.twig-renderer', '@spiffy.view.twig.twig-resolver']],

    'spiffy.view.json-view-strategy' => 'Spiffy\View\JsonStrategy'
];

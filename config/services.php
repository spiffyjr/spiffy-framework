<?php

return [
    // Twig Configuration
    'twig.environment' => ['Twig_Environment', ['@twig.loader.filesystem', '$framework[twig][options]']],
    'twig.loader.filesystem' => ['Spiffy\Framework\Twig\TwigLoaderFactory'],

    // View Services
    'spiffy.framework.view.json-strategy' => ['Spiffy\Framework\View\JsonStrategy', ['@spiffy.view.json-strategy']],

    'spiffy.view.twig.twig-resolver' => ['Spiffy\View\Twig\TwigResolver', ['@twig.environment']],
    'spiffy.view.twig.twig-renderer' => ['Spiffy\View\Twig\TwigRenderer', ['@twig.environment', '@spiffy.view.twig.twig-resolver']],
    'spiffy.view.twig.twig-strategy' => ['Spiffy\View\Twig\TwigStrategy', ['@spiffy.view.twig.twig-renderer', '@spiffy.view.twig.twig-resolver']],

    'spiffy.view.json-strategy' => 'Spiffy\View\JsonStrategy'
];

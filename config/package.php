<?php

$config = [
    'framework' => [
        /*
         * An array of key/value pairs that specify the action name along with the dispatchable to call when
         * the action is called.
         */
        'actions' => [],

        /*
         * Setup the Twig_Environment which is the default View Strategy.
         */
        'twig' => [
            /*
             * A list of extensions to register. They can be anything the Injector accepts.
             */
            'extensions' => [],

            /*
             * A list of paths for the Twig_Loader_Filesystem. If you use a custom loader then this option
             * is never used.
             */
            'paths' => [],

            /*
             * A list of options that get passed directly to Twig_Environment.
             */
            'options' => [
                'cache' => 'cache/twig',
                'debug' => isset($_ENV['debug']) && $_ENV['debug'],
            ],
        ],

        /*
         * Configuration for the Spiffy\Framework\ViewManager which is responsible for taking the return results
         * from actions and picking the correct renderer for them.
         */
        'view_manager' => [
            /*
             * The template for exceptions that are caught during the application's event life-cycle.
             */
            'exception_template' => 'error/exception',

            /*
             * The template for rendering 404's from invalid routes or actions.
             */
            'not_found_template' => 'error/404',

            /*
             * An array of strategies to register with the view manager. Strategies inspect the return result from
             * actions and decide how to handle them. The strategy names can be a service name to pull from the
             * Injector.
             */
            'strategies' => [
                'spiffy.view.twig.twig-strategy',
                'spiffy.view.json-view-strategy',
            ],

            /*
             * The default strategy to use if no other strategy can handle the action result. The canRender() method on
             * this strategy is not-verified because it *must* be able to handle any results.
             */
            'fallback_strategy' => 'Spiffy\View\VardumpStrategy',
        ],

        /*
         * An array of routes with the following specification:
         *
         * 'route_name' => ['route', 'action (dispatchable, invokable, closure)', ['additional options']]
         *
         * Alternatively, if your package implements ApplicationPackage routes can be returned through the getRoutes()
         * method using the same format above.
         */
        'routes' => []
    ]
];

if (isset($_ENV['debug']) && $_ENV['debug']) {
    $config['framework']['twig']['extensions'][] = 'Twig_Extension_Debug';
}

return $config;

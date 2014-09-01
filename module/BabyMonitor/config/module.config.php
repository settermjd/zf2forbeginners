<?php

return array(
    'router' => array(
        'routes' => array(
            'feeds' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/baby-monitor',
                    'defaults' => array(
                        '__NAMESPACE__' => 'BabyMonitor\Controller',
                        'controller'    => 'Feeds',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'action' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'pages'
                            ),
                        ),
                    ),
                    'delete' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/feeds/delete[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'delete',
                            )
                        ),
                        'may_terminate' => true,
                    ),
                    'manage' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/feeds/manage[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'manage',
                            )
                        ),
                        'may_terminate' => true,
                    ),
                    'search' => array(
                        'type'    => 'segment',
                        'options' => array(
                            'route'    => '/feeds/search[/:startDate][/:endDate][/]',
                            'constraints' => array(
                                'startDate' => '(19|20)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])',
                                'endDate'   => '(19|20)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])',
                            ),
                            'defaults' => array(
                                'controller' => 'BabyMonitor\Controller\Feeds',
                                'action' => 'Search'
                            ),
                        ),
                    ),
                    'view' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/feeds/view[/:id]',
                            'constraints' => array(
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'view',
                            )
                        ),
                        'may_terminate' => true,
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(),
        'factories' => array(
            'BabyMonitor\Controller\Feeds' =>
                'BabyMonitor\Controller\Factories\FeedsControllerFactory',
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'template_map' => array(
            'BabyMonitor\Feeds\mytemplate' => __DIR__ . '/../view/baby-monitor/feeds/results/search-simple.phtml',
            'layout/index-action-layout' => __DIR__ . '/../view/layout/layout.phtml',
            'babymonitor/feeds/results/search/default' => __DIR__ . '/../view/baby-monitor/feeds/feed-results.phtml',
            'babymonitor/feeds/results/defaultloop' => __DIR__ . '/../view/baby-monitor/feeds/feed-results-loop.phtml',
        ),
    ),
);
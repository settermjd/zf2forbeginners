<?php
return array(
    'app' => array(
        'name' => 'Baby Management Application',
        'webmaster' => array(
            'name' => 'Matthew Setter',
            'email' => 'matthew@maltblue.com'
        )
    ),
    'cache' => array(
        'adapter' => array(
            'name' => 'filesystem',
            'options' => array(
                'cache_dir' => __DIR__ . '/../../data/cache/'
            )
        ),
        'plugins' => array(
            'exception_handler' => array(
                'throw_exceptions' => false
            ),
            'serializer'
        )
    ),
);
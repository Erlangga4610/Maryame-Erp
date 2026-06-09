<?php

return [
    'default' => 'notyf',

    'main_script' => '/vendor/flasher/flasher.min.js',

    'inject_assets' => true,

    'translate' => true,

    'flash_bag' => [
        'success' => ['success'],
        'error' => ['error', 'danger'],
        'warning' => ['warning', 'alarm'],
        'info' => ['info', 'notice', 'alert'],
    ],

    'plugins' => [
        'notyf' => [
            'scripts' => ['/vendor/flasher/flasher-notyf.min.js'],
            'styles' => ['/vendor/flasher/flasher-notyf.min.css'],
            'options' => [
                'position' => [
                    'x' => 'right',
                    'y' => 'bottom',
                ],
                'duration' => 5000,
                'dismissible' => true,
            ],
        ],
    ],
];

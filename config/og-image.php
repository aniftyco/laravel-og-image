<?php

use HeadlessChromium\Page;

return [
    'no_sandbox' => false,
    'ignore_certificate_errors' => true,
    'custom_flags' => [
        '--disable-gpu',
        '--disable-dev-shm-usage',
        '--disable-setuid-sandbox',
    ],
    'event_name' => Page::NETWORK_IDLE,
    'view_port' => [
        'width' => 1200,
        'height' => 630,
    ],
    'name' => 'og-image',
    'cache' => [
        'maxage' => 60 * 60 * 24 * 7, // 1 week
        'revalidate' => 60 * 60 * 24, // 1 day
    ]
];

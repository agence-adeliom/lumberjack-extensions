<?php

return [
    'smtp' => [
        'hostname' => getenv('MAILER_HOST') ?: '',
        'username' => getenv('MAILER_USERNAME') ?: '',
        'password' => getenv('MAILER_PASSWORD') ?: '',
        'auth' => getenv('MAILER_AUTH') ?: false,
        'port' => getenv('MAILER_PORT') ?: '25',
    ],
];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'discord' => [
        'prefix'    => '/',
        'not_found' => 'Command Not Found!',
        'path'      => [
            'commands' => app_path('Discord/Commands'),
            'directs'  => app_path('Discord/Directs'),
        ],
        'token'     => env('DISCORD_BOT_TOKEN'),
        'channel'   => env('DISCORD_CHANNEL'),
        'bot'       => env('DISCORD_BOT'),
        'yasmin'    => [
            'ws.disabledEvents' => [
                'TYPING_START',
            ],
        ],
    ],
];

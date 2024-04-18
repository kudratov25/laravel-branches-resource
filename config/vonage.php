<?php

return [
    'key' => env('VONAGE_KEY'),
    'send' => env('VONAGE_SEND', false),
    'secret' => env('VONAGE_SECRET'),
    'attempts' => env('VONAGE_ATTEMPTS'),
    'sms_from' => env('VONAGE_SMS_FROM'),
];

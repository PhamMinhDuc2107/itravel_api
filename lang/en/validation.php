<?php

return [
    'email' => [
        'required' => 'Email is required.',
        'email'    => 'Email must be a valid email address.',
        'max'      => 'Email may not be greater than :max characters.',
    ],

    'password' => [
        'required' => 'Password is required.',
        'min'      => 'Password must be at least :min characters.',
        'max'      => 'Password may not be greater than :max characters.',
    ],
];

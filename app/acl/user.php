<?php
// таблица доступа для контроллера
return [
    'all' => [        
        'user',
        'users',        
    ],
    'authorize' => [
        'sessions',
        'logout',
        'profile',
    ],
    'guest' => [
        'login',
        'signup',
        'forgotpassword',
        'resetpassword',
        'activate',        
    ],
    'admin' => [
        //
    ],
];
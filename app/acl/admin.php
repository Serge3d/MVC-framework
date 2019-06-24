<?php
// таблица доступа для контроллера
return [
    'all' => [
        'login',
    ],
    'authorize' => [
        //
    ],
    'guest' => [
        //
    ],
    'admin' => [
        'posts',
        'logout',
        'add',
        'edit',
        'delete',
    ],
];
<?php
// Связь страниц сайта с соответствующим контроллером
return [
    // MainController
    '' => ['main','index'],
    'main/index/{page:\d+}' => ['main','index'],
    'about' => ['main','about'],
    'contact' => ['main','contact'],
    'post/{id:\d+}' => ['main','post'],    
    // UserController
    'profile' => ['user','profile'],
    'user/{login:\w+}' => ['user','user'],
    'users' => ['user','users'],
    'login' => ['user','login'],
    'logout' => ['user','logout'],
    'signup' => ['user','signup'],
    'activate/{token:\w+}' => ['user','activate'],
    'sessions' => ['user','sessions'],    
    'forgotpassword' => ['user','forgotpassword'],
    'resetpassword/{token:\w+}' => ['user','resetpassword'],
    // ChatController
    'chats' => ['chat', 'chats'],
    'chat/{id:\d+}' => ['chat','chat'],
    'send/{id:\d+}' => ['chat','send'],
    'get/{id:\d+}' => ['chat','get'],
    'startdialog/{login:\w+}' => ['chat','startdialog'],
    // AdminController
    'admin' => ['admin','login'],
    'admin/login' => ['admin','login'],
    'admin/logout' => ['admin','logout'],
    'admin/add' => ['admin','add'],
    'admin/edit/{id:\d+}' => ['admin','edit'],
    'admin/delete/{id:\d+}' => ['admin','delete'],
    'admin/posts/{page:\d+}' => ['admin','posts'],
    'admin/posts' => ['admin','posts'],    
];
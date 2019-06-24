<?php

namespace app\core;

class View {
    
    public $path;
    public $route;
    public $layout = 'default';
    
    public function __construct($route) {
        $this->route = $route;
        $this->path = $route['controller'].'/'.$route['action'];
    }
    
    // Отображение страницы
    public function render($title, $vars = []) {
        // Извлекаем переменные из массива $vars
        extract($vars);
        // Путь до view
        $path = 'app/views/'.$this->path.'.php';
        if (file_exists($path)) {
            // Включение буферизации вывода
            ob_start();
            // Загрузка view
            require $path;
            // Очистить (стереть) буфер вывода и отключить буферизацию вывода
            $content = ob_get_clean();
            // Загрузка макета
            require 'app/views/layouts/'.$this->layout.'.php';
        }
        exit;
    }

    // Возвращает шаблон
    public function getTemplate($template, $vars = []) {
        // Езвлекаем переменные из массива $vars
        extract($vars);
        // Путь до шаблона
        $path = 'app/views/templates/' . $template . '.php';
        if (file_exists($path)) {
            // Включение буферизации вывода
            ob_start();
            // Загрузка
            include $path;
            // Очистить (стереть) буфер вывода и отключить буферизацию вывода
            return ob_get_clean();            
        }
        return false;
    }
    
    // Отображение информационного сообщения с текущим шаблоном $layout
    public function showMessage($title, $message, $url = false, $link = 'Ссылка') {
        // Путь до шаблона сообщений
        $this->path = 'templates/message';
        $vars['message'] = $message;
        // Если передана ссылка, передаем её в шаблон сообщения
        if ($url !== false) {
            $vars['url'] = $url;
            $vars['link'] = $link;
        }
        $this->render($title, $vars);
    }

    // Перенаправление на указанную страницу
    public static function redirect($url, $fullUrl = false) {
        // Если передается не полный адрес, добавляем в начало адреса "/"
        if (!$fullUrl) {
            $url = '/' . $url;
        }
        header('location: '.$url);
        exit;
    }

    // Перенаправление на предыдущую страницу. Если её нет, перенаправление на начальную страницу
    public function redirectBack() {
        if (@$_SERVER['HTTP_REFERER'] != null) {
            $this->redirect($_SERVER['HTTP_REFERER'], true);
        } else {
            $this->redirect('');
        }        
    }
    
    // Показ ошибки (403, 404 и т.д.)
    public static function errorCode($code) {
        http_response_code($code);
        $path = 'app/views/errors/'.$code.'.php';
        if (file_exists($path)) {
            require $path;
        }    
        exit;
    }    
    // Вывод сообщения (alert) с использованием ajax
    public function message($status, $message)
    {
        exit(json_encode(['status' => $status, 'message' => $message]));
    }
    // Переадресация с использованием ajax
    public function location($url)
    {
        exit(json_encode(['url' => $url]));
    }
    // Передача в браузер данных с использованием ajax
    public function sendData($data)
    {
        exit(json_encode($data));
    }
}
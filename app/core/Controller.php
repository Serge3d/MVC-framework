<?php

namespace app\core;

use app\core\View;
use app\models\User;

abstract class Controller {
    
    public $route;
    public $view;
    public $acl;
    public $vars = [];
    public $user;
    
    public function __construct($route) {
        $this->route = $route;

        // Проверяем сессию и куки
        $userModel = new User;
        if (isset($_COOKIE['Hash']) && !isset($_SESSION['user'])) {
            // Если сессия не начата, но есть куки, проверяем их достоверность
            $this->user = $userModel->sessionValidate($_COOKIE['Hash']);
            $userModel->startSession($this->user, true);
        } elseif (isset($_SESSION['user'])) {
            $this->user = $userModel->getUser('id', $_SESSION['user']);             
        }
        // Если пользователь авторизован, записываем в БД последнее время его активности
        if ($this->user) {
            $userModel->setLastActivity($this->user['id']);
        }
        // Проверка доступа к данной странице. Если доступа нет, перенаправляем на главную страницу
        if (!$this->checkAcl()) {
            View::redirect('');
        }
        $this->view = new View($route);
        $this->model = $this->loadModel($route['controller']);
        $this->userModel = $userModel;
               
        // Записываем данные о пользователе
        $this->vars['user_nicename'] = $this->user['nicename'];
        $this->vars['user_login'] = $this->user['login'];
        $this->vars['user_email'] = $this->user['email'];
        
    }

    public function loadModel($name)
    {
        $path = 'app\models\\'.ucfirst($name);
        if (class_exists($path)) {
            return new $path;
        }
    }

    public function checkAcl()
    {
        $this->acl = require 'app/acl/'.$this->route['controller'].'.php';
        if ($this->isAcl('all')) {
           return true;
        } elseif (isset($_SESSION['user']) and $this->isAcl('authorize')) {
            return true;
        } elseif (!isset($_SESSION['user']) and $this->isAcl('guest')) {
            return true;
        } elseif (isset($_SESSION['admin']) and $this->isAcl('admin')) {
            return true;
        }
        return false;
    }

    public function isAcl($key) {
        return in_array($this->route['action'], $this->acl[$key]);
    }
    
}
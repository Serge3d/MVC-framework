<?php

namespace app\controllers;

use app\core\Controller;
use app\lib\Pagination;
use app\models\Admin;
use app\lib\Smtp;

class MainController extends Controller {
        
    public function __construct($route)
    {
        parent::__construct($route);
    }

    // Начальная страница
    public function indexAction() {
        if (isset($this->route['page'])) {
            $page = $this->route['page'];
        } else {
            $page = 1;
        }
        $pagination = new Pagination($page, $this->model->postsCount(), '');
        $this->vars += [
            'pagination' => $pagination->get(),
            'list' => $this->model->postsList($this->route),
            'user' => $this->user,
        ];     
        $this->view->render('Главная страница', $this->vars);
    }

    //Страница "Обо мне"
    public function aboutAction() {         
        $this->view->render('Обо мне', $this->vars);
    }
    
    // Страница обратной связи
    public function contactAction() {
        if (!empty($_POST)) {
            if (!$this->model->contactValidate($_POST)) {
                $this->view->message('error', $this->model->error);
            }
            // Формируем текст письма
            $name = $_POST['name'];
            $email = $_POST['email'];
            $text = $_POST['text'];
            $message = require 'app/views/templates/feedBack.php';
            // Формируем атрибуты письма
            $to      = 'sergey_3d@mail.ru';
            $subject = 'Сообщение из блога';            

            //Отправка письма
            $mailSuccess = $this->sendMail($to, $subject, $message);
            
            if ($mailSuccess === true) {
                $this->view->message('success', 'Сообщение отправлено');
            }
            $this->view->message('error', 'Сообщение не отправлено. Ошибка: '.$mailSuccess);
        }
        $this->view->render('Контакты', $this->vars);
    }
    
    // Страница просмотра поста
    public function postAction() {  
        $adminModel = new Admin;
        if (!$adminModel->isPostExists($this->route['id'])) {
            $this->view->errorCode(404);
        }    
        $this->vars += [
            'data' => $adminModel->postData($this->route['id'])[0],
            'idPrevious' => 1,
            'idNext' => 3,
        ];
        $this->view->render('Пост', $this->vars);
    }

}
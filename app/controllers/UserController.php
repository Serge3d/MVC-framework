<?php

namespace app\controllers;

use app\core\Controller;
use app\lib\Pagination;
use app\lib\Rudate;

class UserController extends Controller {
    
    public function __construct($route)
    {
        parent::__construct($route);
    }
    
    // Страница со списком пользователей
    public function usersAction() {  
        
        $this->vars['users'] = $this->model->usersList();
        $this->view->render('Пользователи', $this->vars);
    }

    // Страницы профилей пользователей
    public function userAction() {                     
        $user = $this->model->getUser('login', $this->route['login']);        
        // Если пользователь не найден, выдаем ошибку
        if (!$user) {
            $this->view->showMessage('Ошибка', 'Пользователь не найден');
        }
        // Если запрошен профиль текущего пользователя, перенаправляем на страницу полного профиля
        if ($user['id'] === $this->user['id']) {
            $this->view->redirect('profile');
        }

        // Получаем данные о пользователе       
        $this->vars['login'] = $user['login'];
        $this->vars['nicename'] = $user['nicename'];
        $this->vars['registered'] = Rudate::getRuDate(date_create($user['registered'])->format('d F Y'));
        $this->vars['lastActiviti'] = $this->model->getLastActivity($user['id']);
                       
        $this->view->render('Профиль', $this->vars);
    }

    // Страница профиля пользователя
    public function profileAction() {        
        // Проверяем, передан ли логин в url. Если нет, используем логин авторизованного пользователя
        if (!isset($this->route['login'])) {
            $user = $this->user;
        } else {
            $user = $this->model->getUser('login', $this->route['login']);
        }
        // Если пользователь не найден, выдаем ошибку
        if (!$user) {
            $this->view->showMessage('Ошибка', 'Пользователь не найден');
        }

        $currentUser = $user == $this->user;

        // Получаем данные о пользователе
        $this->vars['login'] = $this->user['login'];
        $this->vars['nicename'] = $this->user['nicename'];
        $this->vars['registered'] = Rudate::getRuDate(date_create($this->user['registered'])->format('d F Y'));        
        $this->vars['email'] = $this->user['email'];       
                
        $this->view->render('Профиль', $this->vars);
    }

    // Страницы последних сессий пользователя
    public function sessionsAction() {        
        // Получаем данные о пользователе
        $this->vars['login'] = $this->user['login'];
        $this->vars['sessionList'] = $this->model->sessionList($this->user['id']);
                
        $this->view->render("Сессии пользователя {$this->user['login']}", $this->vars);
    }

    // Страница авторизации
    public function loginAction() {
        if (!empty($_POST)) {
            $user = $this->model->loginValidate($_POST['login'], $_POST['password']);
            if ($user) {
                // Проверка на подтвержение Email при регистрации
                if (!is_null($user['activation_key'])) {
                    $this->view->showMessage('Ошибка', 'Профиль ещё не активирован. Для повторной отправки на Ваш Email письма подтверждения регистрации воспользуйтесь ссылкой: ', '/forgotpassword');
                }
                $this->model->startSession($user, isset($_POST['remember']));
                $this->view->redirectBack();
            } 
            $this->vars['error'] = $this->model->error;
            $this->vars += $_POST;
        }

        $this->view->render('Вход', $this->vars);
    }

    // Страница регистрации
    public function signupAction() {
        // Проверка введенных регистрационных данных
        if (!empty($_POST)) {
            $user = $this->model->signup($_POST);
            // Если регистрация прошла успешно, авторизуем нового пользователя
            if ($user) {                               
                $this->view->showMessage('Завершение регистрации', 'На указанный Email направлено письмо. Перейдите по указанной в письме ссылке для завершения регистрации');                
            } 
            $this->vars['error'] = $this->model->error;
            $this->vars += $_POST;
        } 
        $this->view->render('Регистрация', $this->vars);
    }

    // Подтверждение Email
    public function activateAction() {
        $user = $this->model->checkActivateToken($this->route['token']);
        // Если пользователь не найден, выдаем ошибку
        if (!$user) {
            $this->view->showMessage('Ошибка', 'Ссылка недействительна');
        }                      
        $this->view->showMessage('Регистрация завершена', 'Теперь вы можете авторизоваться, нажав кнопку "Войти"', '/login', 'Войти');
    }

    // Страница Забыли пароль
    public function forgotpasswordAction() {
        // Проверка введенных данных
        if (!empty($_POST)) {
            $user = $this->model->forgotPassword($_POST['email']);
            if ($user) {
                $this->view->showMessage('Успешно', 'Ссылка на сброс пароля направлена на указанный Email');
            }       
            $this->vars['error'] = $this->model->error;
            $this->vars['email'] = $_POST['email'];
        }
        $this->view->render('Забыли пароль?', $this->vars);
    }

    // Страница сброса пароля
    public function resetpasswordAction() { 
        $user = $this->model->checkResetToken($this->route['token']);
        // Если пользователь не найден, выдаем ошибку
        if (!$user) {
            $this->view->showMessage('Ошибка', $this->model->error[0]);
        } 
        if (!empty($_POST)) {            
            $success = $this->model->resetPassword($user['id'], $_POST['password'], $_POST['passwordconfirm']);
            if ($success) {
                $this->view->showMessage('Пароль изменён', 'Теперь вы можете авторизоваться, нажав кнопку "Войти"', '/login', 'Войти');
            }
            $this->vars['error'] = $this->model->error;
        }
        $this->vars['token'] = $this->route['token'];
        $this->view->render('Сброс пароля', $this->vars);
    }

    // Выход
    public function logoutAction() { 
        $this->model->stopSession();
        $this->view->redirectBack();
    }
}
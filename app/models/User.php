<?php

namespace app\models;

use app\core\Model;
use app\lib\RandomToken;

class User extends Model
{    
    // Проверка связки логин-пароль или Email-пароль
    public function loginValidate($login, $password)
    {        
       
        $user = $this->getUser('login', $login);
        if (!$user) {
            $user = $this->getUser('email', $login);
        }

        if ($user and password_verify($password, $user['password'])) {
            return $user;
        }
        
        $this->error[] = 'Неверный логин/Email или пароль';
        return false;
    }

    // Проверка токена сессии
    public function sessionValidate($token)
    {
        // Если передана пустая строка, поиск не производим
        if (!$token) {
            $this->error[] = 'Ошибка загрузки сессии';
            return false;
        }

        $params = [
            'token' => $token,
        ]; 
        $query = 'SELECT * FROM users WHERE remember_token = :token';
        $user = $this->db->getRow($query, $params);
        if ($user) { 
            return $user;
        }        
        $this->error[] = 'Ошибка загрузки сессии';
        return false;
    }
    
    // находим пользователя по значению $value поля $field
    public function getUser($field, $value)
    {
        // Если поле указано неверно, поиск не производим
        $fields = array("id", "login", "email");
        if (!in_array($field, $fields)) {
            return false;
        }
        $params = [
            $field => $value,
        ];
        $user = $this->db->getRow("SELECT * FROM users WHERE {$field} = :{$field}", $params);

        return $user;
    }

    // Список всех пользователей
    public function usersList()
    {
        $users = $this->db->getAll('SELECT login, nicename, registered FROM users');

        return $users;
    }
    
    // Список сессий пользователя с ID = $id
    public function sessionList($user_id)
    {        
        $params = [
            'user_id' => $user_id,
        ];
        $query = 'SELECT meta_value FROM usermeta WHERE user_id = :user_id AND meta_key = "sessions" ';
        $sessions = $this->db->getCol($query, $params);
        return unserialize($sessions);
    }

    // Регистрация нового пользователя
    public function signup($post)
    {
        $error = [];
        $login = $post['login'];
        $password = $post['password'];
        $email = $post['email'];

        // Проверка введенных данных       
        if (!$this->checkLogin($login) || !$this->checkPassword($password) || !$this->checkEmail($email)) {
            return false;
        }

        // Проверка существования аккаунта с такими логином и/или почтой
        if ($this->getUser('login', $login)) {
            $error[] = 'Пользователь с таким логинон зарегестрирован, выберите другой';
        }
        if ($this->getUser('email', $email)) {
            $error[] = 'Пользователь с таким E-mail зарегестрирован, выберите другой';
        }
        // Если выявлены ошибки, возвращаем false
        if (count($error) !== 0) {
            $this->error += $error;
            return false;
        }

        // Вносим аккаунт в базу данных
        $params = [
            'login' => $login,
            'nicename' => $login,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'registered' => date("Y-m-d H:i:s"),
            'activation_key' => RandomToken::create(16),
        ];
        $query = "INSERT INTO users SET login = :login, nicename = :nicename, email = :email, password = :password, registered = :registered, activation_key = :activation_key";
        $success = $this->db->query($query, $params);

        // Отправляем письмо с подтверждением и возвращаем зарегестрированного пользователя
        if ($success) {
            $user = $this->getUser('login', $login);
            // Отправка письма подтверждения Email
            $this->sendActivationMail($user);
            return $user;
        }
        
        $this->error[] = 'Ошибка создания пользователя';
        return false;
    }

    // Проверка корректности логина
    public function checkLogin($login) {
        $error = [];
        $loginLen = iconv_strlen($login, 'UTF-8');

        if (!preg_match("#^[a-zA-Z0-9_\-]+$#", $login)) {
            $error[] = 'Логин должен содержать только латинские буквы, цифры, знак подчеркивания («_»), точку («.»), минус («-»)';
        }
        if ($loginLen < 3 or $loginLen > 20) {
            $error[] = 'Логин должен содержать от 3 до 20 символов';
        }
        if (count($error) !== 0) {
            $this->error += $error;
            return false;
        }
        return true;
    }

    // Проверка корректности email
    public function checkEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error[] = 'E-mail указан не верно';
            return false;
        }
        return true;
    }

    // Проверка корректности пароля
    public function checkPassword($password, $password2 = false) {
        $passLen = iconv_strlen($password, 'UTF-8');
        if ($passLen < 3 or $passLen > 30) {
            $this->error[] = 'Пароль должен содержать от 3 до 30 символов';            
            return false;
        }      
        if ($password2 !== false && $password2 !== $password) {
            $this->error[] = 'Пароли не совпадают';            
            return false;
        }
        return true;
    }

    // Смена пароля
    public function resetPassword($userID, $newPassword, $newPassword2 = false)
    {
        $error = [];        
        // Проверка введенных данных        
        if (!$this->checkPassword($newPassword, $newPassword2)) {
            return false;
        }        
        // Проверка существования аккаунта с такими id
        if (!$this->getUser('id', $userID)) {
            $this->error[] = 'Пользователь не найден';
            return false;
        }

        // Вносим изменения в базу данных
        $params = [
            'id' => $userID,            
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'remember_token' => NULL,
        ];
        $query = "UPDATE users SET password = :password, remember_token = :remember_token  WHERE id = :id";
        $success = $this->db->query($query, $params);

        // Возвращаем true в случае успеха
        if ($success) {
            return true;
        }
        
        $this->error[] = 'Ошибка изменения пароля';
        return false;
    }

    // Проверка токена сброса пароля
    public function checkResetToken($resetToken) {
        
        if (!$resetToken) {
            $this->error[] = 'Ссылка сброса пароля не действительна';
            return false;
        }

        $params = [
            'reset_token' => $resetToken,
        ];
        $query = 'SELECT * FROM migrations WHERE reset_token = :reset_token';
        $result = $this->db->getRow($query, $params);

        if (!$result) {
            $this->error[] = 'Ссылка сброса пароля не действительна';
            return false;
        }

        if ($result['requested'] < time() - (2 * 60 * 60)) {
            $this->error[] = 'Ссылка сброса пароля просрочена';
            return false;
        }

        $user = $this->getUser('id', $result['user_id']);
        
        if ($user) {
           return $user;
        }

        $this->error[] = 'Пользователь не найден';
        return false;
    }

    // Проверка токена активации профиля
    public function checkActivateToken($token) {        
        if (!$token) {
            return false;
        }
        $params = [
            'token' => $token,
        ];
        $query = 'SELECT * FROM users WHERE activation_key = :token';
        $user = $this->db->getRow($query, $params);

        if (!$user) {
            return false;
        }

        $params = [
            'id' => $user['id'],
        ];
        $query = 'UPDATE users SET activation_key = NULL WHERE id = :id';
        $success = $this->db->query($query, $params);
                
        return $user;
    }

    // Подготовка к сбросу пароля (или направление повторного письма активации)
    public function forgotPassword($email) {
        if ($this->checkEmail($email)) {            
            $user = $this->getUser('email', $email);
            if ($user) {
                if (!is_null($user['activation_key'])) {
                    // Отправка письма подтверждения Email
                    $this->sendActivationMail($user);
                    return $user;
                }
                $user['reset_token'] = RandomToken::create(16);
                $params = [
                    'user_id' => $user['id'],
                    'reset_token' => $user['reset_token'],
                    'requested' => time(),
                ];
                $query = 'INSERT INTO migrations SET user_id = :user_id, reset_token = :reset_token, requested = :requested';
                
                $success = $this->db->query($query, $params);

                //Отправляем письмо сброса пароля на почту
                $this->sendForgotMail($user);

                return $user;
            }
        }
        $this->error[] = 'Пользователь с таким email не найден';        
        return false;
    }
    // Запуск сессии для авторизованного пользователя
    public function startSession($user, $remember = false) {
        // Если пользователь не передан, ничего не делаем        
        if (!$user) {
            return false;
        }
       
        // Если выбрано запомнить сеанс, а в БД не создан токен, создаем его и записываем в БД
        if ($remember && is_null($user['remember_token'])) {
            $user['remember_token'] = RandomToken::create(16);
            $params = [
                'id' => $user['id'],
                'token' => $user['remember_token'],
            ];
            $query = 'UPDATE users SET remember_token = :token WHERE id = :id';        
            $success = $this->db->query($query, $params);
        }

        // Получение IP адреса клиента
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = @$_SERVER['REMOTE_ADDR']; 
        if(filter_var($client, FILTER_VALIDATE_IP)) $ip = $client;
        elseif(filter_var($forward, FILTER_VALIDATE_IP)) $ip = $forward;
        else $ip = $remote;

        // Подготовка данных для записи в таблицу
        $session = [
            'auth' => date("Y-m-d H:i:s"),
            'IP' => $ip,
            'agent' => @$_SERVER['HTTP_USER_AGENT'],
        ];

        $sessions = $this->sessionList($user['id']);

        if (!$sessions) {
            $sessions[] = $session;
            $params = [
                'user_id' => $user['id'],
                'meta_key' => 'sessions',
                'meta_value' => serialize($sessions),
            ];
            $query = "INSERT INTO usermeta SET user_id = :user_id, meta_key = :meta_key, meta_value = :meta_value";
            $success = $this->db->query($query, $params);
        } else {
            $sessions[] = $session;
            $params = [
                'user_id' => $user['id'],
                'meta_key' => 'sessions',
                'meta_value' => serialize(array_slice($sessions, -10)),
            ];
            $query = "UPDATE usermeta SET meta_value = :meta_value WHERE user_id = :user_id AND meta_key = :meta_key";
            $success = $this->db->query($query, $params);
        }
        
        // Создание сессии
        $_SESSION['user'] = $user['id'];
        // Запись куки
        if ($remember) {
            setcookie("Hash", $user['remember_token'], time()+60*60*24*10, NULL, NULL, NULL, TRUE);
        }
    }

    // Остановка сессии
    public function stopSession() {
        $userId = $_SESSION['user'];       

        unset($_SESSION['user']);
        if (isset($_COOKIE['Hash'])) {
            setcookie("Hash", '', time()-1);          
        }
    }

    // Отправка письма активации профиля
    public function sendActivationMail($user) {

        if (is_null($user['activation_key'])) {
            $this->error[] = 'Профиль уже активирован';
            return false;
        }
        // Отправляем письмо с подтверждением регистрации на почту                
        $url = "{$this->config['url']}/activate/{$user['activation_key']}";
        $login = $user['login'];
        $message = require 'app/views/templates/registrationConfirm.php';

        $to      = $user['email'];
        $subject = 'Регистрация на dev.h1n.ru';
               
        //Отправка письма
        return $this->sendMail($to, $subject, $message);
    }

    // Остановка письма сброса пароля
    public function sendForgotMail($user) {

        // Отправляем письмо с подтверждением сброса пароля на почту
        $url = $this->config['url'];
        $urlReset = "{$this->config['url']}/resetpassword/{$user['reset_token']}";
        $login = $user['login'];
        $message = require 'app/views/templates/forgotpassword.php';

        $to      = $user['email'];
        $subject = 'Подтверждение сброса пароля для ' . $url;
                    
        //Отправка письма
        return $this->sendMail($to, $subject, $message);
    }

    // Запись в БД времени последней активности пользователя
    public function setLastActivity($user_id) {
       // Если пользователя с таким id не найдено, возвращаем false
       $user = $this->getUser('id', $user_id);
       if (!$user) {
           return false;
       }

       $lastActivity = $this->getLastActivity($user_id);
       $params = [
            'user_id' => $user_id,
            'meta_key' => 'lastActivity',
            'meta_value' => date("Y-m-d H:i:s"),
        ];

       if (!$lastActivity) {            
            $query = "INSERT INTO usermeta SET user_id = :user_id, meta_key = :meta_key, meta_value = :meta_value";            
        } else {            
            $query = "UPDATE usermeta SET meta_value = :meta_value WHERE user_id = :user_id AND meta_key = :meta_key";            
        }
        $success = $this->db->query($query, $params);
    }

    // Получение времени последней активности пользователя
    public function getLastActivity($user_id) {
        $params = [
            'user_id' => $user_id,
        ];
        $query = 'SELECT meta_value FROM usermeta WHERE user_id = :user_id AND meta_key = "lastActivity"';
        $lastActivity = $this->db->getRow($query, $params);
        if ($lastActivity) {
            return $lastActivity['meta_value'];
        }
        return false;
    }
}
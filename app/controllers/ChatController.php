<?php

namespace app\controllers;

use app\core\Controller;
use app\lib\Pagination;
use app\lib\Rudate;

class ChatController extends Controller {
    
    public function __construct($route)
    {
        parent::__construct($route);
        $this->view->layout = 'chat';
    }
    
    // Все переписки пользователя
    public function chatsAction() {
        
        $chatList = $this->model->chatList($this->user['id']);
        foreach ($chatList as &$chat) {
            $chat['members'] = $this->model->chatMemberList($chat['chat_id'], $this->user['id']);
            // Получаем количество непрочитанных сообщение в чате, диалоге
            $chatTypes = array("dialog", "chat");
            if (in_array($chat['type'], $chatTypes)) {
                $chat['unread'] = $this->model->getUnreadCount($this->user['id'], $chat['chat_id']);
            }
            if ($chat['type'] === 'dialog') {
                $user = $this->userModel->getUser('id', $chat['members'][0]['user_id']);
                $chat['name'] = $user['nicename'];
            } else {
                $chat['name'] = $this->model->getChatName($chat['chat_id']);
            }
        }

        $this->vars['login'] = $this->user['login'];
        $this->vars['chatList'] = $chatList;
        $this->view->render("Сообщения", $this->vars);
    }

    // Окно чата
    public function chatAction() {
        $chats = $this->model->chatList($this->user['id'], $this->route['id']);
        if (!$chats) {
            $this->view->redirect('chats');
        }
        $chat = $chats[0];
        $chat['members'] = $this->model->chatMemberList($chat['chat_id'], $this->user['id']);
        foreach ($chat['members'] as &$member) {
            $member += $this->userModel->getUser('id', $member['user_id']);
        }
                
        if ($chat['type'] === 'dialog') {
            $chat['name'] = $chat['members'][0]['nicename'];
        } else {
            $chat['name'] = $this->model->getChatName($chat['chat_id']);
        }
        
        $this->vars['chat'] = $chat;
        $this->view->render("Сообщения", $this->vars);
    }

    // Получение сообщений из чата с помощью AJAX
    public function getAction() {
        $chatId = $this->route['id'];
        $chats = $this->model->chatList($this->user['id'], $chatId);
        // Если чата не существует, перенаправляем на страницу списка чатов
        if (!$chats) {
            $this->view->location('chats');
        }
        // Если передан id первого сообщения, ищем более ранние, если последнего, ищем новые сообщения
        if (isset($_POST['firstMessageId'])) {
            $list = $this->model->getOldMessages($chatId, $this->user['id'], $_POST['firstMessageId']);
        } else {
            $list = $this->model->getNewMessages($chatId, $this->user['id'], $_POST['lastMessageId']);
        }
        
        // Если сообщений нет, возвращаем информацию об этом
        if (!$list) {
            $this->view->sendData(['html' => false]);
        }
        // Костыль, переделать
        // Заполняем массив выводимых сообщений
        $firstMessageId = $list[count($list)-1]['message_id'];
        $lastMessageId = $list[0]['message_id'];
        $listReversed = array_reverse($list);
        $messages = [];
        $users = [];
        foreach ($listReversed as $message) {
            $messageId = $message['message_id'];
            $userId = $message['user_id'];
            // Если данного пользователя еще не получали, загружаем его
            if (!isset($users[$userId])) {
                $users[$userId] = $this->userModel->getUser('id', $userId);
            }

            $messages[$messageId] = $message;
            $messages[$messageId] += $this->model->getMessage($messageId);
            $messages[$messageId] += [
                'nicename' => $users[$userId]['nicename'],
                'login' => $users[$userId]['login'],
            ];
        }
        
        $html = $this->view->getTemplate('chatMessages', ['messages' => $messages, 'login' => $this->user['login']]);

        $vars = [
            'html' => $html,
            'firstMessageId' => $firstMessageId,
            'lastMessageId' => $lastMessageId,
            'post' => $_POST,
        ];
        $this->view->sendData($vars);
        
    }

    // Отправка сообщения с помощью AJAX
    public function sendAction() {
        $chats = $this->model->chatList($this->user['id'], $this->route['id']);
        if (!$chats) {
            $this->view->location('chats');
        }

        $chat = $chats[0];
        // Если передано сообщение, записываем его в БД
        if (!empty($_POST)) {
            $this->model->addMessage($this->user['id'], $chat['chat_id'], $_POST);

            $this->view->message('success', 'Сообщение успешно отправлено');
        }
                
        //$this->view->redirect("chat/{$this->route['id']}");
    }

    // Начало диалога. Если переписка уже создана, переход в неё
    public function startdialogAction() {
        // Если пользователь не авторизован, перенаправляем на страницу входа
        if (!$this->user) {
            $this->view->redirect('login');
        }
        $user = $this->userModel->getUser('login', $this->route['login']);
        // Если пользователь не найден, выдаем ошибку
        if (!$user) {
            $this->view->showMessage('Ошибка', 'Пользователь не найден');
        }

        $dialog = $this->model->startDialog($this->user['id'], $user['id']);
        
        // Перенаправляем на страницу чата
        $this->view->redirect("chat/{$dialog['chat_id']}");        
    }
}
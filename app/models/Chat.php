<?php

namespace app\models;

use app\core\Model;
use app\lib\RandomToken;

class Chat extends Model
{    
    // Типы чатов: dialog, chat, ...
    // Типы участников: admin, member, left, ...
    // Типы содержимого сообщений: text, image, link, likes, ...


    // Список чатов пользователя с ID = $id. Если передан id чата, то вернет этот чат если он существует
    public function chatList($user_id, $chat_id = false)
    {        
        //Получаем список чатов пользователя
        $params = [
            'user_id' => $user_id,
        ];
        $query = 'SELECT c.chat_id, c.type FROM chat c 
        INNER JOIN chat_members m ON c.chat_id = m.chat_id
        WHERE m.user_id = :user_id';
        // Если передан id чата, дополняем запрос
        if ($chat_id !== false) {
            $params += [
                'chat_id' => $chat_id,
            ];
            $query .=' AND c.chat_id = :chat_id';
        }
        $chatList = $this->db->getAll($query, $params);        

        return $chatList;
    }

    // Получение количества непрочитанных сообщений пользователем. Если передаен чат, то в этом чате
    public function getUnreadCount($user_id, $chat_id = false)
    {
        
        // Если передан id чата, ищем количество по нему
        if ($chat_id !== false) {
            $params = [
                'user_id' => $user_id,
                'chat_id' => $chat_id,
            ];
            $query ='SELECT COUNT(mtu.message_id) FROM message_to_user mtu
                INNER JOIN message m ON mtu.message_id = m.message_id 
                WHERE mtu.user_id = :user_id AND mtu.readed = 0 AND mtu.deleted = 0 AND m.chat_id = :chat_id';
        } else {
            $params = [
                'user_id' => $user_id,
            ];
            $query = 'SELECT COUNT(*) FROM message_to_user WHERE user_id = :user_id AND readed = 0 AND deleted = 0';
        }
        
        $count = $this->db->getCol($query, $params);
        return $count;
    }

    // Получить переписку двух пользователей (диалог)
    public function getDialog($user1_id, $user2_id)
    {        
        //Получаем список чатов пользователя
        $params = [
            'user1' => $user1_id,
            'user2' => $user2_id,
        ];
        $query = 'SELECT c.chat_id FROM chat c 
        INNER JOIN chat_members m1 ON c.chat_id = m1.chat_id
        INNER JOIN chat_members m2 ON c.chat_id = m2.chat_id
        WHERE m1.user_id = :user1 AND m2.user_id = :user2 AND c.type = "dialog"';
        
        $dialog = $this->db->getRow($query, $params);
        
        return $dialog;
    }

    // Начинаем диалог и возвращаем его. Если диалог уже создан, возвращаем созданный
    public function startDialog($user1_id, $user2_id)
    {        
        $dialog = $this->getDialog($user1_id, $user2_id);
        if (!$dialog) {
            // Добавляем диалог            
            $dialog = $this->addChat('dialog');

            // Добавляем участников чата
            $this->addMember($user1_id, $dialog['chat_id'], 'admin');
            $this->addMember($user2_id, $dialog['chat_id'], 'admin');
        }        
        return $dialog;
    }

    // Добавляем участника в чат
    public function addMember($user_id, $chat_id, $status)
    {        
        $params = [
            'user_id' => $user_id,                
            'chat_id' => $chat_id,
            'status' => $status,
        ];
        $query = 'INSERT INTO chat_members SET user_id = :user_id, chat_id = :chat_id, status = :status';
        $success = $this->db->query($query, $params);  

        return $success;
    }

    // Добавляем чат в БД и возвращаем его
    public function addChat($type)
    {        
        // Получаем уникальный не занятый токен для нового чата
        do {
            $token = RandomToken::create(16);
        } while ($this->getChat($token));

        $params = [
            'type' => $type,
            'token' => $token,
        ];
        $query = 'INSERT INTO chat SET type = :type, token = :token';
        $success = $this->db->query($query, $params);

        if (!$success) {
            $this->error[] = 'Ошибка создания чата';
            return false;
        }

        $chat = $this->getChat($token);

        return $chat;
    }

    // Найти чат по уникальному токену 
    public function getChat($token)
    {        
        $params = [
            'token' => $token,
        ];
        $query = 'SELECT * FROM chat WHERE token = :token';
        $chat = $this->db->getRow($query, $params);

        return $chat;        
    }

    // Список участников чата. Если передан пользователь, то исключая его из списка
    public function chatMemberList($chat_id, $user_id = false)
    {
        $params = [
            'chat_id' => $chat_id,
        ];
        $query = 'SELECT user_id, status FROM chat_members WHERE chat_id = :chat_id';

        if ($user_id !== false) {
            $params += [
                'user_id' => $user_id,
            ];
            $query .=' AND user_id <> :user_id';
        }

        $chatMemberList = $this->db->getAll($query, $params);
        return $chatMemberList;
    }

    // Название чата. Если тип чата - диалог
    public function getChatName($chat_id)
    {        
        $params = [
            'chat_id' => $chat_id,
        ];
        $query = 'SELECT value FROM chat_property WHERE chat_id = :chat_id AND name = "name"';
        $chatName = $this->db->getCol($query, $params);

        return $chatName;        
    }

    // Тип чата. 
    public function getChatType($chat_id)
    {
        
        $params = [
            'chat_id' => $chat_id,
        ];
        $query = 'SELECT type FROM chat WHERE chat_id = :chat_id';
        $chatType = $this->db->getCol($query, $params);

        return $chatType;        
    }

    // Список всех сообщений чата. Если задан $user_id - список видимых этому пользователю сообщений
    public function messageList($chat_id, $user_id = false)
    {        
        if ($user_id === false) {
            $params = [
                'chat_id' => $chat_id,
            ];
            $query = 'SELECT * FROM message WHERE chat_id = :chat_id';            
        } else {
            $params = [
                'chat_id' => $chat_id,
                'user_id' => $user_id,
            ];            
            $query = 'SELECT m.* FROM message m 
                INNER JOIN message_to_user mtu ON m.message_id = mtu.message_id
                WHERE mtu.user_id = :user_id AND m.chat_id = :chat_id AND mtu.deleted = 0
                ORDER BY m.message_id DESC';            
        }
        
        $messageList = $this->db->getAll($query, $params);

        return $messageList;
    }

    // Список новых сообщений в чате после указанного
    public function getNewMessages($chat_id, $user_id, $message_id = 0)
    {        
        $params = [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
            'message_id' => $message_id,
        ];            
        $query = 'SELECT m.* FROM message m 
            INNER JOIN message_to_user mtu ON m.message_id = mtu.message_id
            WHERE mtu.user_id = :user_id AND m.chat_id = :chat_id AND mtu.deleted = 0 AND m.message_id > :message_id
            ORDER BY m.message_id DESC';
        
        $messages = $this->db->getAll($query, $params);
        return $messages;
    }

    // Список старых сообщений в чате до указанного. Если передан 0, то список самых новых сообщений. Лимит - 20
    public function getOldMessages($chat_id, $user_id, $message_id = 0)
    {        
        if ($message_id == 0) {
            $params = [
                'chat_id' => $chat_id,
                'user_id' => $user_id,
            ];
            $query = 'SELECT m.* FROM message m 
                INNER JOIN message_to_user mtu ON m.message_id = mtu.message_id
                WHERE mtu.user_id = :user_id AND m.chat_id = :chat_id AND mtu.deleted = 0
                ORDER BY m.message_id DESC LIMIT 20'; 
        } else {
            $params = [
                'chat_id' => $chat_id,
                'user_id' => $user_id,
                'message_id' => $message_id,
            ];
            $query = 'SELECT m.* FROM message m 
                INNER JOIN message_to_user mtu ON m.message_id = mtu.message_id
                WHERE mtu.user_id = :user_id AND m.chat_id = :chat_id AND mtu.deleted = 0 AND m.message_id < :message_id
                ORDER BY m.message_id DESC LIMIT 20'; 
        }
        
        $messages = $this->db->getAll($query, $params);
        return $messages;
    }

    // Получение всех параметров сообщения из таблицы message_property в виде 'name => value'
    public function getMessage($message_id)
    {
        $params = [
            'message_id' => $message_id,
        ];
        $query = 'SELECT name, value FROM message_property WHERE message_id = :message_id';
        $messageProperty = $this->db->getAll($query, $params);

        $message = [];

        foreach ($messageProperty as $property) {
            $message[$property['name']] = $property['value'];
        }

        return $message;
    }

    // Получение последнего сообщения пользователя
    public function getLastUserMessage($user_id)
    {
        $params = [
            'user_id' => $user_id,
        ];
        $query = 'SELECT * FROM message WHERE user_id = :user_id ORDER BY message_id DESC LIMIT 1';
        $message = $this->db->getRow($query, $params);
        
        return $message;
    }

    // Добавление сообщения в чат
    public function addMessage($user_id, $chat_id, $content)
    {
        
        $params = [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
            'send' => date("Y-m-d H:i:s"),
        ];
        $query = 'INSERT INTO message SET user_id = :user_id, chat_id = :chat_id, send = :send';
        $success = $this->db->query($query, $params);
        if (!$success) {
            $this->error[] = 'Ошибка добавления сообщения';
            return false;
        }
        // Получаем Последнее добавленное сообщение
        $message = $this->getLastUserMessage($user_id);
        
        foreach ($content as $name => $value) {
            $params = [
                'message_id' => $message['message_id'],
                'name' => $name,
                'value' => $value,
            ];
            $query = 'INSERT INTO message_property SET message_id = :message_id, name = :name, value = :value';
            $success = $this->db->query($query, $params);
        }
        // Если тип чата - диалог или чат, направляем сообщение всем участникам чата в таблицу message_to_user
        $types = array("dialog", "chat");
        $chatType = $this->getChatType($chat_id);
        if (in_array($chatType, $types)) {
            $params = [
                'message_id' => $message['message_id'],
                'user_id' => $user_id,
            ];
            $query = 'INSERT INTO message_to_user SET message_id = :message_id, user_id = :user_id, readed = 1';
            $success = $this->db->query($query, $params);

            $members = $this->chatMemberList($chat_id, $user_id);
            foreach ($members as $member) {
                $params = [
                    'message_id' => $message['message_id'],
                    'user_id' => $member['user_id'],
                ];
                $query = 'INSERT INTO message_to_user SET message_id = :message_id, user_id = :user_id';
                $success = $this->db->query($query, $params);
            }
        }

        return true;        
    }
    
}
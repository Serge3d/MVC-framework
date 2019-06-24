<?php

namespace app\models;

use app\core\Model;

class Main extends Model
{
    
    public function contactValidate($post)
    {
        $nameLen = iconv_strlen($post['name'], 'UTF-8');
        $textLen = iconv_strlen($post['text'], 'UTF-8');
        if ($nameLen < 3 or $nameLen > 20) {
            $this->error[] = 'Имя должно содержать от 3 до 20 символов';
        }
        if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error[] = 'E-mail указан не верно';
        } 
        if ($textLen < 5 or $textLen > 500) {
            $this->error[] = 'Сообщение должно содержать от 5 до 500 символов';            
        }

        // Если ошибок нет ,возвращаем true
        return count($this->error) === 0;
    }

    public function postsCount() {
        return $this->db->getCol('SELECT COUNT(id) FROM posts');
    }
    
    public function postsList($route)
    {
        $max = 10;
        $params =[
            'max' => $max,
            'start' => (($route['page'] ?? 1) - 1) * $max,
        ];
        return $this->db->getAll('SELECT * FROM posts ORDER BY id DESC LIMIT :start, :max', $params);
    }
}
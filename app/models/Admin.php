<?php

namespace app\models;

use app\core\Model;
use Imagick;

class Admin extends Model
{
    
    public function loginValidate($post)
    {
        $config = require 'app/config/admin.php';
        if ($config['login'] != $post['login'] or $config['password'] != $post['password']) {
            $this->error[] = 'Неправильно введен логин и/или пароль'; 
            return false;
        } 
        return true;
    }

    public function postValidate($post, $files, $type)
    {
        $nameLen = iconv_strlen($post['name'], 'UTF-8');
        $descriptionLen = iconv_strlen($post['description'], 'UTF-8');
        $textLen = iconv_strlen($post['text'], 'UTF-8');
        if ($nameLen < 3 or $nameLen > 100) {
            $this->error[] = 'Название должно содержать от 3 до 100 символов';
            return false;
        } elseif ($descriptionLen < 3 or $descriptionLen > 500) {
            $this->error[] = 'Описание должно содержать от 3 до 500 символов';
            return false;
        } elseif ($textLen < 5 or $textLen > 50000) {
            $this->error[] = 'Текст должен содержать от 5 до 50000 символов';
            return false;
        } elseif ($type == 'add' and !$files['img']['tmp_name']) {
            $this->error[] = 'Изображение не выбрано';
            return false;
        }
        return true;
    }

    public function postAdd($post)
    {
        $params = [
            'name' => htmlspecialchars($post['name']),
            'description' => htmlspecialchars($post['description']),
            'text' => htmlspecialchars($post['text']),
            'date' => date("Y-m-d H:i:s"),
        ];
        $query = "INSERT INTO `posts`(`name`, `description`, `text`, `date`) VALUES (:name, :description, :text, :date)";
        $this->db->query($query, $params);
        return $this->db->lastInsertId();
    }

    public function postEdit($post, $id)
    {
         $params = [
            'id' => $id,
            'name' => htmlspecialchars($post['name']),
            'description' => htmlspecialchars($post['description']),
            'text' => htmlspecialchars($post['text']),
            'date' => date("Y-m-d H:i:s"),
        ];
        $query = "UPDATE posts SET name = :name, description = :description, text = :text, date = :date WHERE id = :id";
        $this->db->query($query, $params);
    }

    public function postUploadImage($files, $id)
    {
        $img = new Imagick($files['img']['tmp_name']);
        $img->cropThumbnailImage(1080, 600);
        $img->setImageCompressionQuality(80);
        $img->writeImage('public/images/postimages/'.$id.'.jpg');
    }

    public function isPostExists($id)
    {
        $params = [
            'id' => $id,
        ];
        return $this->db->getCol('SELECT id FROM posts WHERE id = :id',$params);
    }

    public function postDelete($id)
    {
        $params = [
            'id' => $id,
        ];
        $this->db->query('DELETE FROM posts WHERE id = :id', $params);
        unlink('public/images/postimages/'.$id.'.jpg');
    }

    public function postData($id)
    {
        $params = [
            'id' => $id,
        ];
        return $this->db->getAll('SELECT * FROM posts WHERE id = :id',$params);
    }
}
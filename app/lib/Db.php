<?php

namespace app\lib;

use PDO;

class Db {
    
    protected $db;
    
    public function __construct($config) {
        // Если не задана кодировка, используем UTF8
        if (!isset($config['charset'])) {
            $config['charset'] = 'UTF8';
        }
        // Задаем опции
        $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->db = new PDO($dsn, $config['user'], $config['password'], $opt);
    }
    // Выполнение запроса
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            foreach ($params as $key => $val) {
                if (is_int($val)) {
                    $type = PDO::PARAM_INT;
                } else {
                    $type = PDO::PARAM_STR;
                }
                $stmt->bindValue(':'.$key, $val, $type);
            }
        }
        $stmt->execute();
        return $stmt;
    }
    // Получаем строку таблицы
    public function getRow($sql, $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetch();
    }
    // Получаем весь результат запроса
    public function getAll($sql, $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetchAll();
    }
    // Получаем колонку таблицы
    public function getCol($sql, $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetchColumn();        
    }
    // ID последней добавленной строки в таблицу
    public function lastInsertId() {
        return $this->db->lastInsertId();
    }

}
<?php

namespace app\core;

use app\lib\Db;
use app\lib\Smtp;

abstract class Model
{
	public $db;
	public $error = [];
	public $config = [];

	public function __construct()
	{
		$dbConfig = require 'app/config/db.php';
		$this->db = new Db($dbConfig);

		$this->config = require 'app/config/app.php';
	}

	// Отправка писем по email
    public function sendMail($to, $subject, $message)
    {
        // Загрузка настроект для SMTP
        $smtp = require 'app/config/smtp.php';
        // $mailSMTP = new SendMailSmtpClass('логин', 'пароль', 'хост', 'порт', 'кодировка письма');
        $mailSMTP = new Smtp($smtp['username'], $smtp['password'], $smtp['host'], $smtp['port'], $smtp['charset']);
        
        $from = array(
            "dev.h1n.ru", // Имя отправителя
            "info@dev.h1n.ru" // почта отправителя
        );                     
        // отправляем письмо
        $mailSuccess =  $mailSMTP->send($to, $subject, $message, $from);

        return $mailSuccess;
    }
}
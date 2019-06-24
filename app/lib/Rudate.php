<?php

namespace app\lib;

class Rudate {
            
    public static function getRuDate($date) {
        $ruMonth = array(
            "января",
            "февраля",
            "марта",
            "апреля",
            "мая",
            "июня",
            "июля",
            "августа",
            "сентября",
            "октября",
            "ноября",
            "декабря"
        );
        $enMonth = array(
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        );
        
        $ruDate = str_replace($enMonth, $ruMonth, $date);
        return $ruDate;
    }

}
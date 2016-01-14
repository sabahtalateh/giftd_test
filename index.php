<?php

require 'vendor/autoload.php';

$p = new \ConfigReader\Parser("
            name = ivan
            db.user = vasya
            db.password = asd123
            db.driver.type = mysql
        ");

var_dump($p->parse());



<?php

require 'vendor/autoload.php';

$p = new \ConfigReader\Parser("
            db.user = vasya
            db.password = asd123
            db.driver.type = mysql
            db.driver = true
            root = src

            maintain = true
            maintain.maintainer.info.name = Petr
            maintain.maintainer.info.phone = 95123444
            maintain.info.start = 2015-06-01
       ");

var_dump($p->parse());
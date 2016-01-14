<?php

namespace spec\ConfigReader;

use ConfigReader\Exception\InvalidArgumentTypeException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith("");
        $this->shouldHaveType('ConfigReader\Parser');
    }

    function it_accepts_only_strings()
    {
        $this->shouldThrow(new InvalidArgumentTypeException("Argument should be a type of string, integer given"))
            ->during('__construct', [123]);

        $this->shouldThrow(new InvalidArgumentTypeException("Argument should be a type of string, boolean given"))
            ->during('__construct', [true]);

        $this->shouldThrow(new InvalidArgumentTypeException("Argument should be a type of string, array given"))
            ->during('__construct', [[1, 2, 3]]);

        $this->shouldThrow(new InvalidArgumentTypeException("Argument should be a type of string, NULL given"))
            ->during('__construct', [null]);

        //... Check other types
    }

    function it_passes_correct_config_0()
    {
        $this->beConstructedWith("
            db.user = vasya
            db.password = asd123
            db.driver.type = mysql
        ");

        $this->parse()->shouldBe([
            'db' => [
                'user' => 'vasya',
                'password' => 'asd123',
                'driver' => [
                    'type' => 'mysql'
                ]
            ]
        ]);
    }

    function it_passes_correct_config_1()
    {
        $this->beConstructedWith("
            root = src
            db.user = vasya
            db.password = asd123
            db.driver.type = mysql
        ");

        $this->parse()->shouldBe([
            'root' => 'src',
            'db' => [
                'user' => 'vasya',
                'password' => 'asd123',
                'driver' => [
                    'type' => 'mysql'
                ]
            ]
        ]);
    }

    function it_passes_correct_config_2()
    {
        $this->beConstructedWith("
            maintainer = Alexandr_Kravtsov
            root = src

            db.user = vasya
            db.password = asd123
            db.driver.type = mysql

            app.mode = production
        ");

        $this->parse()->shouldReturn([
            'maintainer' => 'Alexandr_Kravtsov',
            'root' => 'src',
            'db' => [
                'user' => 'vasya',
                'password' => 'asd123',
                'driver' => [
                    'type' => 'mysql'
                ]
            ],
            'app' => [
                'mode' => 'production',
            ]
        ]);
    }
}

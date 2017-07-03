<?php

require "src/VkGroup.php";


$config = [
    'confirmation_token' => 'c845bea7',
    'service_token'   => 'f55682c1f55682c1f55682c146f50b89abff556f55682c1ac0e41b0cd377e39cc46a642',
    'access_token' => 'cfcb2705c1f5a289843ac8023aebf2871e16cae0193f07619adc3622dc1090d920b0c8a4ab9b15cbf5aa0',

    'group_id'   => 149619624,
    'background' => 'background.jpg',
    'like_interval' => [
        'from' => '01.07.2017 00:00:00',
        'to' => '', // if empty - now time
    ],
    'user_joined' => [
        'shape' => 'circle',
        'position' => [ // center position
        'x' => 1047,
        'y' => 170,
        ],
        'radius' => 100,
        'first_name' => [
            'font' => 'Roboto-Light.ttf',
            'size' => 28,
            'align' => 'center',
            'color' => [255, 255, 255], // RGB 255,255,255 - White
            'x' => 1047,
            'y' => 310,
            'angle' => 0,
        ],
        'second_name' => [
            'font' => 'Roboto-Light.ttf',
            'size' => 28,
            'align' => 'center',
            'color' => [255, 255, 255],
            'x' => 1047,
            'y' => 345,
            'angle' => 0,
        ],
    ],
    'user_liker' => [
        'shape' => 'circle',
        'position' => [
        'x' => 1388,
        'y' => 170,
        ],
        'radius' => 100,
        'first_name' => [
            'font' => 'Roboto-Light.ttf',
            'size' => 28,
            'align' => 'center',
            'color' => [255, 255, 255],
            'x' => 1388,
            'y' => 310,
            'angle' => 0,
        ],
        'second_name' => [
            'font' => 'Roboto-Light.ttf',
            'size' => 28,
            'align' => 'center',
            'color' => [255, 255, 255],
            'x' => 1388,
            'y' => 345,
            'angle' => 0,
        ],
    ],
];

$VkGroup = new VkGroup();
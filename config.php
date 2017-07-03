<?php

require "src/VkGroup.php";


$config = [
    'confirmation_token' => '',
    'service_token'   => '',
    'access_token' => '',

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
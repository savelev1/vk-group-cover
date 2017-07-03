<?php
ini_set('display_errors', 1);

require 'config.php';

//Receive and decode notification
$data = json_decode(file_get_contents('php://input'));

//Check the "type" field
switch ($data->type) {
    case 'confirmation':
    echo $config['confirmation_token'];
    break;

    case 'group_join':
    $VkGroup->updateCover($data);
    break;

    default: echo 'undefined request type';
}
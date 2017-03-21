<?php

require "src/VkExtension.php";

use Vk\Vk;
use VkExtension\VkExtension;

$appId        = 5924275;
$secret       = '';
$access_token = '';

$group_id     = 141748799;
$user_id      = 92682082;
$bg_image     = 'vk_bg.png';

$Vk = new Vk([
    'app_id' => $appId,
    'secret' => $secret
]);

$VkExtension = new VkExtension([
    'access_token' => $access_token
]);

$user = $Vk->api('users.get', [
    'user_ids' => $user_id,
    'fields'   => 'photo_200'
]);

if (empty($user->error)) {
    if (!empty($user[0]->photo_200)) {
        // put user on cover and save image 'vk_image.jpg'
        $VkExtension->putUserOnCover($user[0], $bg_image);

        $VkExtension->changeGroupCover($group_id, 'vk_image.jpg');

        // notify in vk of change of cover
        $user_ids = [2272011, 302379650];
        $message  = "Cover Update";
        $VkExtension->message($user_ids, $message);
    } else {
        echo 'avatar not found';
    }
} else {
    echo 'user not found';
}

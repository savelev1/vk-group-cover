<?php

require "src/VkExtension.php";

use VkExtension\VkExtension;

/**
 * The VkGroup for Vkontakte PHP SDK
 *
 * @author Savelev Aleksandr, https://github.com/savelev1
 */
class VkGroup
{
  public $app;
  public $group;

  function __construct()
  {
    global $config;

    $this->app = new VkExtension([
        'access_token' => $config['service_token'],
    ]);

    $this->group = new VkExtension([
        'access_token' => $config['access_token'],
    ]);
  }

  public function updateCover($data)
  {
    global $config;

    $this->group->clearCoverImage();

    // put on cover joined user
    $user = $this->app->getUser($data->object->user_id);
    $this->group->setUserOnCover($user, $config['user_joined']);

    // find and put on cover of "liker"
    $liker_id = $this->app->getLikerId();
    $user = $this->app->getUser($liker_id);
    $this->group->setUserOnCover($user, $config['user_liker']);

    $this->sendSuccess();
  }

  private function sendSuccess()
  {
    echo('ok');
  }
}
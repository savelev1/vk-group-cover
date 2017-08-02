<?php

namespace VkExtension;

require "Vk.php";
require "oFile.php";
require "BodyPost.php";
require "CircleCrop.php";

use Vk\Vk;

/**
 * The VkExtension for Vkontakte PHP SDK
 *
 * @author Savelev Aleksandr, https://github.com/savelev1
 */
class VkExtension extends Vk
{
    /**
     * Send message to user
     * @param array $user_ids
     * @param string $message
     */
    public function message($user_ids, $message = '')
    {
        if (is_array($user_ids)) {
            foreach ($user_ids as $user_id) {
                $this->api('messages.send', [
                    'user_id' => $user_id,
                    'message' => $message,
                    ]);
            }
        }
    }

    /**
     * Set user on cover
     * @param object $user - vk user object
     * @param array $options - options for draw
     */
    public function setUserOnCover($user, $options)
    {
        global $config;

        if (empty($user->error)) {
            if (!empty($user[0]->photo_200)) {
                // put user on cover and save image 'vk_image.jpg'
                $this->putUserOnCover($user[0], $options);

                $this->changeGroupCover($config['group_id'], 'cover.jpg');
            } else {
                echo 'avatar not found';
            }
        } else {
            echo 'user not found';
        }
    }

    /**
     * Getting user by id
     * @param integer $user_id - vk user id
     * @return object - vk user object
     */
    public function getUser($user_id = 1)
    {
        return $this->api('users.get', [
            'user_ids' => $user_id,
            'fields'   => 'photo_200',
            ]);
    }

    /**
     * Getting user id who put the most likes
     * @return object - vk user object
     */
    public function getLikerId()
    {
        global $config;

        $users = [];

        $time_to = time();
        if (!empty($config['like_interval']['to'])) {
            $time_to = strtotime($time_to);
        }

        $posts = $this->getPosts(-$config['group_id']);

        foreach ($posts as $post) {
            if ($post->date < strtotime($config['like_interval']['from']) || $post->date > $time_to) {
                continue;
            }

            $likes = $this->getLikes(-$config['group_id'], $post->id);

            foreach ($likes->users as $id) {
                if (isset($users[$id])) {
                    $users[$id]++;
                } else {
                    $users[$id] = 1;
                }
            }
        }

        arsort($users);
        $user_ids = array_keys($users);
        $user_id = array_shift($user_ids);

        return $user_id;
    }

    /**
     * Clear cover
     */
    public function clearCoverImage()
    {
        global $config;

        $background_image  = imagecreatefromjpeg($config['background']);

        list($width, $height) = getimagesize($config['background']);

        $cover_image = imagecreatetruecolor($width, $height);

        imagecopyresampled($cover_image, $background_image, 0, 0, 0, 0, $width, $height, $width, $height);

        imagejpeg($cover_image, 'cover.jpg', 100);
    }

    /**
     * Getting posts
     * @param integer $owner_id - owner post id
     * @param integer $count - count posts
     * @return object - vk posts array
     */
    private function getPosts($owner_id = 1, $count = 100)
    {
        return $this->api('wall.get', [
            'owner_id' => $owner_id,
            'count'   => $count,
            ]);
    }

    /**
     * Getting likes for post
     * @param integer $owner_id - owner post id
     * @param integer $item_id - post id
     * @param string $type - type
     * @return object - vk likes array
     */
    private function getLikes($owner_id = 1, $item_id = 1, $type = 'post')
    {
        return $this->api('likes.getList', [
            'owner_id' => $owner_id,
            'item_id'   => $item_id,
            'type'   => $type,
            ]);
    }

    /**
     * Put user on cover and save 'vk_image.jpg'
     * @param object $user
     */
    private function putUserOnCover($user, $options)
    {
        // create image
        $cover_src  = imagecreatefromjpeg('cover.jpg');
        $user_img = imagecreatefromjpeg($user->photo_200);

        list($width, $height)         = getimagesize($user->photo_200);
        list($new_width, $new_height) = getimagesize('cover.jpg');

        $out_width = $options['radius'] * 2;
        $out_height = $options['radius'] * 2;
        $user_dst = imagecreatetruecolor($out_width, $out_height);
        imagecopyresampled($user_dst, $user_img, 0, 0, 0, 0, $out_width, $out_height, $width, $height);

        $cover_dst = imagecreatetruecolor($new_width, $new_height);

        // put background
        imagecopyresampled($cover_dst, $cover_src, 0, 0, 0, 0, $new_width, $new_height, $new_width, $new_height);

        if ($options['shape'] == 'circle') {
            $crop = new CircleCrop($user_dst);
            $user_dst = $crop->crop()->get();
        }

        $start_x = $options['position']['x'] - $options['radius'];
        $start_y = $options['position']['y'] - $options['radius'];

        // put avatar
        imagecopymerge($cover_dst, $user_dst, $start_x, $start_y, 0, 0, $out_width, $out_height, 100);

        $first_name = $user->first_name;
        $last_name  = $user->last_name;


        $this->putText($cover_dst, $first_name, $options['first_name']);
        $this->putText($cover_dst, $last_name, $options['second_name']);


        // $this->putTextCenter($cover_dst, 310, $first_name);
        // $this->putTextCenter($cover_dst, 350, $last_name);

        // save image
        imagejpeg($cover_dst, 'cover.jpg');
    }

    /**
     * Put text with left align
     * @param Image $img
     */
    private function putText(&$img, $text = '', $options)
    {
        $color_text = imagecolorallocate($img, $options['color'][0], $options['color'][1], $options['color'][2]);

        if ($options['align'] != 'left') {
            $bbox   = imagettfbbox($options['size'], $options['angle'], $options['font'], $text);
            $length = $bbox[2] - $bbox[0];

            if ($options['align'] == 'center') {
                $options['x'] -= $length / 2;
            }

            if ($options['align'] == 'right') {
                $options['x'] -= $length;
            }
        }

        imagettftext($img, $options['size'], $options['angle'], $options['x'], $options['y'], $color_text, $options['font'], $text);
    }

    /**
     * Put text with center align
     * @param Image $img
     * @param integer $y
     * @param string $text
     * @param string $font
     */
    private function putTextCenter(&$img, $y = 32, $text = '', $font = 'troika.ttf')
    {
        $bbox   = imagettfbbox(32, 0, $font, $text);
        $center = (imagesx($img) / 2) - (($bbox[2] - $bbox[0]) / 2);
        $white  = imagecolorallocate($img, 255, 255, 255);

        imagettftext($img, 32, 0, $center, $y, $white, $font, $text);
    }

    /**
     * Change group cover
     * @param integer $group_id
     * @param string $image_name
     * @return bool Result
     */
    private function changeGroupCover($group_id, $image_name)
    {
        list($width, $height) = getimagesize($image_name);

        // get upload url
        $uploadServer = $this->api('photos.getOwnerCoverPhotoUploadServer', [
            'group_id' => $group_id,
            'crop_x'   => 0,
            'crop_y'   => 0,
            'crop_x2'  => $width,
            'crop_y2'  => $height,
            ]);

        if (!empty($response->error)) {
            return false;
        }

        // send photo
        $delimiter = '-------------'.uniqid();
        $file = new oFile($image_name);
        $post = BodyPost::Get(array('photo' => $file), $delimiter);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $uploadServer->upload_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data; boundary='.$delimiter, 'Content-Length: '.strlen($post)));

        $response = curl_exec($ch);

        if (!empty($response->error)) {
            return false;
        }

        $response = json_decode($response);

        // save photo
        $response = $this->api('photos.saveOwnerCoverPhoto', [
            'hash'  => $response->hash,
            'photo' => $response->photo,
            ]);

        if (!empty($response->error)) {
            return false;
        }

        return true;
    }
}

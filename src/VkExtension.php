<?php

namespace VkExtension;

require "Vk.php";
require "oFile.php";
require "BodyPost.php";

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
     * Change group cover
     * @param integer $group_id
     * @param string $image_name
     * @return bool Result
     */
    public function changeGroupCover($group_id, $image_name) {
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

    /**
     * Put user on cover and save 'vk_image.jpg'
     * @param object $user
     * @param string $background
     */
    public function putUserOnCover($user, $background)
    {
        // create image
        $png  = imagecreatefrompng($background);
        $jpeg = imagecreatefromjpeg($user->photo_200);

        list($width, $height)         = getimagesize($user->photo_200);
        list($new_width, $new_height) = getimagesize($background);

        $out = imagecreatetruecolor($new_width, $new_height);

        // put avatar
        imagecopyresampled($out, $jpeg, 685, 42, 0, 0, $width + 20, $height + 20, $width, $height);
        // put background
        imagecopyresampled($out, $png, 0, 0, 0, 0, $new_width, $new_height, $new_width, $new_height);

        $first_name = $user->first_name;
        $last_name  = $user->last_name;

        $this->putTextCenter($out, 310, $first_name);
        $this->putTextCenter($out, 350, $last_name);

        // save image
        imagejpeg($out, 'vk_image.jpg', 100);
    }

    /**
     * Put text with center align
     * @param Image $img
     * @param integer $y
     * @param string $text
     * @param string $font
     */
    private function putTextCenter(&$img, $y = 32, $text = '', $font = 'troika.ttf') {
        $bbox   = imagettfbbox(32, 0, $font, $text);
        $center = (imagesx($img) / 2) - (($bbox[2] - $bbox[0]) / 2);
        $white  = imagecolorallocate($img, 255, 255, 255);

        imagettftext($img, 32, 0, $center, $y, $white, $font, $text);
    }

}
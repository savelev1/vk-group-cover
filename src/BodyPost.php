<?php

namespace VkExtension;

class BodyPost
{
    public static function PartPost($name, $val)
    {
        $body = 'Content-Disposition: form-data; name="' . $name . '"';

        if($val instanceof oFile) {
            $file = $val->Name();
            $mime = $val->Mime();
            $cont = $val->Content();

            $body .= '; filename="' . $file . '"' . "\r\n";
            $body .= 'Content-Type: ' . $mime ."\r\n\r\n";
            $body .= $cont."\r\n";
        } else {
            $body .= "\r\n\r\n".urlencode($val)."\r\n";
        }

        return $body;
    }

    public static function Get(array $post, $delimiter='-------------0123456789')
    {
        if(is_array($post) && !empty($post)) {
            $bool = false;

            foreach($post as $val) {
                if($val instanceof oFile) {
                    $bool = true;
                    break;
                }
            }

            if($bool) {
                $ret = '';

                foreach($post as $name=>$val) {
                    $ret .= '--' . $delimiter. "\r\n". self::PartPost($name, $val);
                }

                $ret .= "--" . $delimiter . "--\r\n";
            } else {
                $ret = http_build_query($post);
            }
        } else {
            throw new \Exception('Error input param!');
        }

        return $ret;
    }
};
<?php

namespace VkExtension;

class oFile
{
    private $name;
    private $mime;
    private $content;

    public function __construct($name, $mime = null, $content = null)
    {
        if(is_null($content)) {
            $info = pathinfo($name);

            if(!empty($info['basename']) && is_readable($name)) {
                $this->name = $info['basename'];
                $this->mime = mime_content_type($name);

                $content = file_get_contents($name);

                if($content!==false) {
                    $this->content = $content;
                }

                else {
                    throw new Exception('Don`t get content - "'.$name.'"');
                }
            } else {
                throw new Exception('Error param');
            }
        } else {
            $this->name = $name;

            if(is_null($mime)) {
                $mime = mime_content_type($name);
            }

            $this->mime = $mime;
            $this->content = $content;
        };
    }

    public function Name() { return $this->name; }

    public function Mime() { return $this->mime; }

    public function Content() { return $this->content; }

};
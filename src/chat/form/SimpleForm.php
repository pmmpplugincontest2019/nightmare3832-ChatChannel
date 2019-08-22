<?php

namespace chat\form;

class SimpleForm extends Form{
    const IMAGE_URL = 'url';
    const IMAGE_PATH = 'path';

    protected $label = [];

    public function __construct(){
        parent::__construct();
        $this->data['type'] = 'form';
        $this->data['title'] = '';
        $this->data['content'] = '';
        $this->data['buttons'] = [];
    }

    public function setTitle(string $text): SimpleForm{
        $this->data['title'] = $text;
        return $this;
    }

    public function setContent(string $text): SimpleForm{
        $this->data['content'] = $text;
        return $this;
    }

    public function addButton(string $text, ?string $imageType = null, string $imagePath = '', ?string $label = null): SimpleForm{
        $content = ['text' => $text];
        if($imageType !== null){
            $content['image']['type'] = $imageType;
            $content['image']['data'] = $imagePath;
        }
        $this->data['buttons'][] = $content;
        $this->label[] = $label ?? count($this->label);
        return $this;
    }

    public function process($data){
        return $this->data['buttons'][$data];
    }
}
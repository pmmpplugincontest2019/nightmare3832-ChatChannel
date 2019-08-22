<?php

namespace chat\form;

class ModalForm extends Form{
    public function __construct(){
        parent::__construct();
        $this->data['type'] = 'modal';
        $this->data['title'] = '';
        $this->data['content'] = '';
        $this->data['button1'] = '';
        $this->data['button2'] = '';
    }

    public function setTitle(string $title): ModalForm{
        $this->data['title'] = $title;
        return $this;
    }

    public function setContent(string $text): ModalForm{
        $this->data['content'] = $text;
        return $this;
    }

    public function setButton1(string $text): ModalForm{
        $this->data['button1'] = $text;
        return $this;
    }

    public function setButton2(string $text): ModalForm{
        $this->data['button2'] = $text;
        return $this;
    }
}
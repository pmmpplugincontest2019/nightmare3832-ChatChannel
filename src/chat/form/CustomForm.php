<?php

namespace chat\form;

class CustomForm extends Form{
    protected $label = [];

    public function __construct(){
        parent::__construct();
        $this->data['type'] = 'custom_form';
        $this->data['title'] = '';
        $this->data['content'] = [];
    }

    public function setTitle(string $title): CustomForm{
        $this->data['title'] = $title;
        return $this;
    }

    public function addLabel(string $text, ?string $label = null): CustomForm{
        $this->addContent([
            'type' => 'label',
            'text' => $text
        ]);
        $this->label[] = $label ?? count($this->label);
        return $this;
    }

    public function addToggle(string $text, bool $default = false, ?string $label = null): CustomForm{
        $this->addContent([
            'type'    => 'toggle',
            'text'    => $text,
            'default' => $default
        ]);
        $this->label[] = $label ?? count($this->label);
        return $this;
    }

    public function addSlider(string $text, int $min, int $max, int $step = -1, int $default = -1, ?string $label = null): CustomForm{
        $content = [
            'type' => 'slider',
            'text' => $text,
            'min'  => $min,
            'max'  => $max
        ];
        if($step !== -1) $content['step'] = $step;
        if($default !== -1) $content['default'] = $default;
        $this->addContent($content);
        $this->label[] = $label ?? count($this->label);
        return $this;
    }

    public function addStepSlider(string $text, array $steps, int $defaultIndex = -1, ?string $label = null): CustomForm{
        $content = [
            'type'  => 'step_slider',
            'text'  => $text,
            'steps' => $steps
        ];
        if($defaultIndex !== -1) $content['default'] = $defaultIndex;
        $this->addContent($content);
        $this->label[] = $label ?? count($this->label);
        return $this;
    }

    public function addDropdown(string $text, array $options, int $default = null, ?string $label = null): CustomForm{
        $this->addContent([
            'type'    => 'dropdown',
            'text'    => $text,
            'options' => $options,
            'default' => $default
        ]);
        $this->label[] = $label ?? count($this->label);
        return $this;
    }

    public function addInput(string $text, string $placeholder = '', ?string $default = null, ?string $label = null): CustomForm{
        $this->addContent([
            'type'        => 'input',
            'text'        => $text,
            'placeholder' => $placeholder,
            'default'     => $default
        ]);
        $this->label[] = $label ?? count($this->label);
        return $this;
    }

    public function process($data){
        $res = [];
        foreach($data as $key => $datum){
            $res[$this->label[$key]] = $datum;
        }
        return $res;
    }

    private function addContent(array $content): void{
        $this->data['content'][] = $content;
    }
}
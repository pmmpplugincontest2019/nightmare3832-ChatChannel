<?php

namespace chat\form;

use chat\event\CustomFormSubmitEvent;
use chat\event\ModalFormClickEvent;
use chat\event\SimpleFormSubmitEvent;
use pocketmine\form\Form as BaseForm;
use pocketmine\Player;

abstract class Form implements BaseForm{
    protected $callback = null;
    protected $uniqueId = null;
    protected $data = [];

    public function __construct(){
    }

    public function setCallable(callable $callable){
        $this->callback = $callable;
        return $this;
    }

    public function getCallable(): ?callable{
        return $this->callback;
    }

    public function getUniqueId(){
        return $this->uniqueId;
    }

    public function setUniqueId(string $uniqueid){
        $this->uniqueid = $uniqueid;
        return $this;
    }

    public function handleResponse(Player $player, $data): void{
        if($data !== null){
            $result = $this->process($data);
            if($this->getCallable() !== null){
                call_user_func_array($this->getCallable(), [
                    $player,
                    $result
                ]);
            }
        }
    }

    public function process($data){
        return $data;
    }

    public function jsonSerialize(){
        return $this->data;
    }
}
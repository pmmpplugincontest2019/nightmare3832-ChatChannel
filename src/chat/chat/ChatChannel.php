<?php

namespace chat\chat;

use chat\player\PlayerData;

abstract class ChatChannel{

    public $id;

    private $member = [];
    private $mute = [];

    private $isPrivate = false;

    const DATA_PRIVATE = 0;

    public function __construct(string $id){
        $this->id = $id;
    }

    public function joinChannel(PlayerData $playerData): void{
        if(empty($this->member[$playerData->getId()])){
            $this->member[$playerData->getId()] = $playerData;
        }
    }

    public function quitChannel(PlayerData $playerData): void{
        unset($this->member[$playerData->getId()]);
    }

    public function getId(): string{
        return $this->id;
    }

    public function getMember() : array{
        return $this->member;
    }

    public function setMute(PlayerData $playerData, string $target, bool $mute = true) : void{
        if($mute) $this->mute[$playerData->getId()][$target] = true;
        else unset($this->mute[$playerData->getId()][$target]);
    }

    public function isMute(PlayerData $playerData, string $target) : bool{
        return isset($this->mute[$playerData->getId()][$target]);
    }

    public function setPrivate() : void{
        $this->isPrivate = true;
    }

    public function isPrivate() : bool{
        return $this->isPrivate;
    }

    public function setSettings(array $setting) : void{
        $this->isPrivate = $setting[self::DATA_PRIVATE];
    }

    public function getSettings() : array{
        return [
            self::DATA_PRIVATE => $this->isPrivate
        ];
    }

    public function chat(PlayerData $playerData, string $msg): void{
    }
}
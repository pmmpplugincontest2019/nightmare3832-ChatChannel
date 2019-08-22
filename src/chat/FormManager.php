<?php

namespace chat;

use chat\chat\ChatManager;
use chat\form\CustomForm;
use chat\form\ModalForm;
use chat\form\SimpleForm;
use chat\player\PlayerDataManager;
use pocketmine\Player;

class FormManager{

    public function __construct(){
    }

    public function showChatForm(Player $player) : void{
        $main = Main::getInstance();
        $form = new SimpleForm();
        $playerData = PlayerDataManager::getPlayerDataByPlayer($player);
        $form->setTitle($main->getMessage("form.chat.title"))
            ->setContent($main->getMessage("form.chat.contents", [
                'name' => ($playerData->chatChannel != null ? $playerData->chatChannel->getId() : $main->globalChannel->getId())
            ]))
            ->addButton($main->getMessage("form.chat.button.setting"))
            ->setCallable([$this, 'responseForm']);
        if($playerData->chatChannel != null){
            $form->addButton($main->getMessage("form.chat.button.players"));
            $form->addButton($main->getMessage("form.chat.button.quit"));
            if($player->isOp()){
                $form->addButton($main->getMessage("form.chat.button.delete"));
            }
        }else{
            $form->addButton($main->getMessage("form.chat.button.search"));
        }
        if($player->isOp()){
            $form->addButton($main->getMessage("form.chat.button.create"));
        }
        $player->sendForm($form);
    }

    public function responseForm(...$result) : void{
        $main = Main::getInstance();
        $playerData = PlayerDataManager::getPlayerDataByPlayer($result[0]);
        switch($result[1]['text']){
            case $main->getMessage("form.chat.button.setting"):
                $form = new CustomForm();
                $form->setTitle($main->getMessage("form.chat.setting.title"))
                    ->addToggle($main->getMessage("form.chat.setting.toggle.global"), $playerData->isGlobal)
                    ->setCallable([$this, 'responseSetting']);
                $playerData->getPlayer()->sendForm($form);
                break;
            case $main->getMessage("form.chat.button.players"):
                $form = new SimpleForm();
                $form->setTitle($main->getMessage("form.chat.players.title"))
                    ->setCallable([$this, 'responsePlayers']);
                foreach($playerData->chatChannel->getMember() as $playerData2){
                    if($playerData2->getName() == $playerData->getName()) continue;
                    $form->addButton(($playerData->chatChannel->isMute($playerData, $playerData->getName()) ? "§c" : "§a").$playerData2->getName());
                }
                $playerData->getPlayer()->sendForm($form);
                break;
            case $main->getMessage("form.chat.button.search"):
                $form = new CustomForm();
                $form->setTitle($main->getMessage("form.chat.search.title"))
                    ->addInput($main->getMessage("form.chat.search.input.title"), $main->getMessage("form.chat.search.input"))
                    ->setCallable([$this, 'responseSearch']);
                $playerData->getPlayer()->sendForm($form);
                break;
            case $main->getMessage("form.chat.button.create"):
                $form = new CustomForm();
                $form->setTitle($main->getMessage("form.chat.create.title"))
                    ->addInput($main->getMessage("form.chat.create.input.title"), $main->getMessage("form.chat.create.input"))
                    ->addToggle($main->getMessage("form.chat.create.toggle.private"))
                    ->setCallable([$this, 'responseCreate']);
                $playerData->getPlayer()->sendForm($form);
                break;
            case $main->getMessage("form.chat.button.quit"):
                if($playerData->chatChannel != null){
                    $playerData->getPlayer()->sendMessage($main->getMessage("message.quit", ["name" => $playerData->chatChannel->getId()]));
                    ChatManager::quitChannel($playerData->chatChannel->getId(), $playerData);
                }
                break;
            case $main->getMessage("form.chat.button.delete"):
                if($playerData->chatChannel == null){
                    $playerData->getPlayer()->sendMessage($main->getMessage("message.delete.fail"));
                    return;
                }else{
                    ChatManager::deleteChannel($playerData->chatChannel->getId());
                }
                break;
        }
    }

    public function responseSetting(...$result) : void{
        $playerData = PlayerDataManager::getPlayerDataByPlayer($result[0]);
        $playerData->isGlobal = $result[1][0];
    }

    public function responsePlayers(...$result) : void{
        $main = Main::getInstance();
        $playerData = PlayerDataManager::getPlayerDataByPlayer($result[0]);
        $form = new ModalForm();
        $target = str_replace("§a", "", str_replace("§c", "", $result[1]['text']));
        $this->selecting[$playerData->getName()] = $target;
        $form->setTitle($main->getMessage("form.chat.players.pickup.title"))
            ->setContent($main->getMessage("form.chat.players.pickup.contents", ["name" => $target]))
            ->setButton1(($playerData->chatChannel->isMute($playerData, $target) ? $main->getMessage("form.chat.players.pickup.mute.off") : $main->getMessage("form.chat.players.pickup.mute.on")))
            ->setButton2($main->getMessage("form.chat.players.pickup.cancel"))
            ->setCallable([$this, "responsePlayersPickup"]);
        $playerData->getPlayer()->sendForm($form);
    }

    private $selecting = [];

    public function responsePlayersPickup(...$result) : void{
        $main = Main::getInstance();
        $playerData = PlayerDataManager::getPlayerDataByPlayer($result[0]);
        if($result[1]){
            if($playerData->chatChannel->isMute($playerData, $this->selecting[$playerData->getName()])){
                $playerData->chatChannel->setMute($playerData, $this->selecting[$playerData->getName()], false);
                $playerData->getPlayer()->sendMessage($main->getMessage("message.mute.off", ["name" => $this->selecting[$playerData->getName()]]));
            }else{
                $playerData->chatChannel->setMute($playerData, $this->selecting[$playerData->getName()]);
                $playerData->getPlayer()->sendMessage($main->getMessage("message.mute.on", ["name" => $this->selecting[$playerData->getName()]]));
            }
        }
    }

    public function responseSearch(...$result) : void{
        $main = Main::getInstance();
        $playerData = PlayerDataManager::getPlayerDataByPlayer($result[0]);
        if($result[1][0] == ""){
            $playerData->getPlayer()->sendMessage($main->getMessage("message.input.empty"));
        }
        $form = new SimpleForm();
        $form->setTitle($main->getMessage("form.chat.search.result.title"))
            ->setContent($main->getMessage("form.chat.search.result.contents"))
            ->setCallable([$this, "responseSearchResult"]);
        foreach(ChatManager::$channels as $id => $channel){
            if(strpos($id, $result[1][0]) !== false){
                if($channel->isPrivate() && !$playerData->getPlayer()->isOp()) continue;
                $form->addButton($id);
            }
        }
        $playerData->getPlayer()->sendForm($form);
    }

    public function responseSearchResult(...$result) : void{
        $main = Main::getInstance();
        $playerData = PlayerDataManager::getPlayerDataByPlayer($result[0]);
        if(isset(ChatManager::$channels[$result[1]["text"]])){
            ChatManager::joinChannel($result[1]["text"], $playerData);
            $playerData->getPlayer()->sendMessage($main->getMessage("message.join.success", ["name" => $result[1]["text"]]));
        }else{
            $playerData->getPlayer()->sendMessage($main->getMessage("message.join.fail", ["name" => $result[1]["text"]]));

        }
    }

    public function responseCreate(...$result) : void{
        $main = Main::getInstance();
        $playerData = PlayerDataManager::getPlayerDataByPlayer($result[0]);
        if($result[1][0] == ""){
            $playerData->getPlayer()->sendMessage($main->getMessage("message.input.empty"));
        }
        $channel = ChatManager::createChannel($result[1][0]);
        if($result[1][1]) $channel->setPrivate();
        $playerData->getPlayer()->sendMessage($main->getMessage("message.create.success", ["name" => $result[1][0]]));
    }

}
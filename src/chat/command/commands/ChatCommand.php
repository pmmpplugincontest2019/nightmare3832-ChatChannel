<?php

namespace chat\command\commands;

use chat\command\ChatPluginCommand;
use chat\FormUtil;
use chat\Main;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class ChatCommand extends ChatPluginCommand{

    public function __construct(Plugin $plugin){
        parent::__construct('chat', $plugin);
        $this->setAliases(['1vs1']);
        $this->setDescription('chat Command');
        $this->setUsage('/chat');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!($sender instanceof Player)){
            $sender->sendMessage(Main::getInstance()->getMessage("command.error.1"));
            return true;
        }
        Main::getInstance()->getFormManager()->showChatForm($sender);

        return true;
    }
}
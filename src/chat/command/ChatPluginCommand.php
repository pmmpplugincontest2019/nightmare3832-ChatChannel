<?php

namespace chat\command;

use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;

abstract class ChatPluginCommand extends PluginCommand{
    public function __construct($name, Plugin $owner){
        parent::__construct($name, $owner);
    }
}

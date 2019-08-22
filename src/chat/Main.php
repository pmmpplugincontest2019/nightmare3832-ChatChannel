<?php

namespace chat;

use chat\chat\ChatManager;
use chat\command\RegistrationCommands;
use chat\listener\RegistrationEvents;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase{

    private static $instance = null;

    private $formManager = null;
    private $listener = [];

    public $playerConfig;
    public $channelConfig;
    public $setting;

    public $message = [];

    public $globalChannel;

    public function onEnable(){
        if(self::$instance === null){
            self::$instance = $this;
            if(!file_exists($this->getDataFolder() . "message.ini")){
                copy(__DIR__."/resource/message.ini", $this->getDataFolder() . "message.ini");
            }
            $this->message = array_map('stripcslashes', parse_ini_file($this->getDataFolder() . "message.ini", false, INI_SCANNER_RAW));
            $this->playerConfig = new Config($this->getDataFolder().'player.json', Config::JSON);
            $this->channelConfig = new Config($this->getDataFolder().'channels.json', Config::JSON);
            $this->setting = new Config($this->getDataFolder()."setting.yml", Config::YAML, [
                "chat.speed.restriction.visitor" => 3,
                "chat.speed.restriction.op" => 5
            ]);
            foreach($this->channelConfig->getAll() as $id => $setting){
                $channel = ChatManager::createChannel($id);
                $channel->setSettings($setting);
            }
            $this->register();
            $this->getServer()->getLogger()->info(' §a' . $this->getName() . ' is Loaded!');
        }
    }

    public function registerCommands(): void{
        foreach(RegistrationCommands::REGISTRATION_COMMANDS as $command){
            $cmd = 'chat\\command\\commands\\' . $command;
            $this->getServer()->getCommandMap()->register('Chat', new $cmd($this));
        }
    }

    public function registerEvents(): void{
        foreach(RegistrationEvents::REGISTRATION_EVENTS as $event){
            $ev = 'chat\\listener\\' . $event;
            $this->listener[$event] = new $ev();
            $this->getServer()->getPluginManager()->registerEvents($this->listener[$event], $this);
        }
        $this->listener = array_change_key_case($this->listener);
    }

    public function getListener(string $eventName){
        return $this->listener[strtolower($eventName)] ?? null;
    }

    public function register(): void{
        $this->formManager = new FormManager();

        $this->registerCommands();
        $this->registerEvents();

        $this->globalChannel = ChatManager::createGlobalChannel('global');
    }

    public function onDisable(){
        $this->channelConfig->save();
        $this->playerConfig->save();
    }

    public function getFormManager() : ?FormManager{
        return $this->formManager;
    }

    public static function getInstance(): Main{
        return self::$instance;
    }

    public function getMessage(string $key, array $data = []) : string{
        if(empty($this->message[$key])) return $key;
        $text = $this->message[$key];
        foreach($data as $k => $d){
            $text = str_replace('{' . $k . '}', $d, $text);
        }
        return $text;
    }
}

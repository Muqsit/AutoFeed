<?php

/*
*
*   _|_|                _|                _|_|_|_|                          _|  
* _|    _|  _|    _|  _|_|_|_|    _|_|    _|        _|_|      _|_|      _|_|_|  
* _|_|_|_|  _|    _|    _|      _|    _|  _|_|_|  _|_|_|_|  _|_|_|_|  _|    _|  
* _|    _|  _|    _|    _|      _|    _|  _|      _|        _|        _|    _|  
* _|    _|    _|_|_|      _|_|    _|_|    _|        _|_|_|    _|_|_|    _|_|_|
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*/
namespace Muqsit;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\command\{Command, CommandSender};
use pocketmine\command\{TextFormat as TF, Config};
use pocketmine\event\player\PlayerJoinEvent;

class Main extends PluginBase implements Listener{

  public function onEnable(){
    $this->saveDefaultConfig();
    $this->reloadConfig();
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    $this->feedings = new Config($this->getDataFolder()."feeding.yml", Config::YAML);
  }
  
  public function onJoin(PlayerJoinEvent $e){
    $p = $e->getPlayer();
    if(!isset($this->feedings->get($p->getName))) $this->feedings->set($p->getName(), false);
  }
  
  public function toggleAutoFeed(Player $player){
    if($this->feedings->get($player->getName()) === "false"){
      $this->feedings->set($player->getName(), "true");
      $player->sendMessage(TF::GREEN.TF::BOLD."AutoFeed".TF::RESET.TF::GREEN." Enabled auto feeding.");
    }else{
      $this->feedings->set($player->getName(), "false");
      $player->sendMessage(TF::GREEN.TF::BOLD."AutoFeed".TF::RESET.TF::GREEN." Disabled auto feeding.");
    }
  }
  
  public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
    if(strtolower($cmd->getName()) == "autofeed"){
      if($sender->hasPermission("autofeed")){
        $this->toggleAutoFeed($sender);
        return true;
      }else{
        $sender->sendMessage(TF::RED.TF::BOLD."AutoFeed".TF::RESET.TF::RED." You are not allowed to use this command.");
        return;
      }
    }
  }
}

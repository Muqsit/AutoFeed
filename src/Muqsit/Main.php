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
use pocketmine\{Server, Player};
use pocketmine\command\{Command, CommandSender};
use pocketmine\utils\{TextFormat as TF, Config};
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\{Item, Food, FoodSource};
use pocketmine\event\entity\EntityEatItemEvent;

class Main extends PluginBase implements Listener{

  public function onEnable(){
    if(!file_exists($this->getDataFolder() . "feeding.yml")){
      @mkdir($this->getDataFolder());
      file_put_contents($this->getDataFolder() . "feeding.yml",$this->getResource("feeding.yml"));
    }
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new AutoFeed($this), 20);
    $this->feedings = new Config($this->getDataFolder()."feeding.yml", Config::YAML);
		$this->feedings->save(true);
  }
  
  public function onJoin(PlayerJoinEvent $e){
    $p = $e->getPlayer();
    if($this->feedings->get($p->getName()) === null) $this->feedings->set($p->getName(), false);
  }
  
  public function toggleAutoFeed(Player $player){
    if($this->feedings->get($player->getName()) === "false"){
      $this->feedings->set($player->getName(), "true");
      $player->sendMessage(TF::GREEN.TF::BOLD."AutoFeed".TF::RESET.TF::GREEN." Enabled auto feeding.");
			$this->feedings->save(true);
    }else{
      $this->feedings->set($player->getName(), "false");
      $player->sendMessage(TF::GREEN.TF::BOLD."AutoFeed".TF::RESET.TF::GREEN." Disabled auto feeding.");
			$this->feedings->save(true);
    }
  }

	public function isAutoFed(Player $player){
		if($this->feedings->get($player->getName()) === "true") return true;
		else return false;
	}

	public function autoFeed(Player $player){
		foreach($player->getInventory()->getContents() as $item){
			if($item instanceof Food){
				$feedLevel = 20 - $item->getFoodRestore();
				if($player->getFood() <= $feedLevel){
					$item->setCount($item->getCount() - 1);
					$player->setFood($player->getFood() + $item->getFoodRestore());
				}
			}
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

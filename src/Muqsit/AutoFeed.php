<?php
namespace Muqsit;
use pocketmine\scheduler\PluginTask;
use pocketmine\Player;

class AutoFeed extends PluginTask{

  public function __construct(Main $plugin){
    $this->plugin = $plugin;
    parent::__construct($plugin);
  }

  public function onRun($tick){
		foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
			if($p->hasPermission("autofeed.me")){
				if($this->plugin->isAutoFed($p)) $this->plugin->autoFeed($p);
			}
		}
	}
}

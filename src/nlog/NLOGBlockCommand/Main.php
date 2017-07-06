<?php

namespace nlog\NLOGBlockCommand;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class Main extends PluginBase implements Listener{

 	 public function onEnable(){
    	$this->getServer()->getPluginManager()->registerEvents($this, $this);
    	$this->getLogger()->notice("커맨드를 잠금/잠금해제 할 수 있습니다.");
    	$this->getLogger()->notice("Made by NLOG (nlog.kro.kr)");
    	
    	@mkdir($this->getDataFolder(), 0744, true);
    	$this->cmd = new Config($this->getDataFolder() . "command.yml", Config::YAML);
 	 }
 	 
 	 //커맨드 API
 	 public function getCmd() {
 	 	/*
 	 	 * 커맨드를 Config에서 가져옵니다.
 	 	 */
 	 	return $this->cmd->getAll(true);
 	 }
 	 	
 	 public function isCmd($command) {
 	 	/*
 	 	 * 커맨드가 잇으면 true, 없으면 false를 반환합니다.
 	 	 */
 	 	return $this->cmd->exists($command);
 	 }
 	 	
 	 public function setCmd($command) {
 	 	$this->cmd->set($command, "command");
 	 	$this->cmd->save();
 	 	return true;
 	 }
 	 	
 	 public function removeCmd($command) {
 	 	$this->cmd->remove($command, "command");
 	 	$this->cmd->save();
 	 }
 	 
 	 
 	 public function onCommand(CommandSender $sender,Command $cmd, $label,array $args) {
 	 	
 	 	$msg = "§b§o[ 알림 ] §7/command <lock | unlock> <command>\n§b§o[ 알림 ] §7/command list";
 	 	
 	 	if(strtolower($cmd->getName() === "command")) {
 	 		if (!($sender->isOp())) {
 	 			$sender->sendMessage("§b§o [ 알림 ] §7권한이 없습니다.");
 	 			return true; //OP 가 아닐 때 - 안전빵으로 한번 더ㅋㅋ
 	 		}
 	 		if (!(isset($args[0]))) {
 	 			$sender->sendMessage($msg);
 	 			return true;
 	 		}
 	 		#-----------------------------------------------------------------------------
 	 		if ($args[0] === "lock") {
 	 			if (!(isset($args[1]))) {
 	 				$sender->sendMessage($msg);
 	 				return true;
 	 			} //커맨드가 입력하지 않았을 때
 	 	
 	 			/*if ($this->getServer()->getCommandMap()->getCommand(strtolower($args[1])) === null) {
 	 				$sender->sendMessage("§b§o [ 알림 ] §7명령어가 존재하지 않습니다.");
 	 				return true;
 	 			} //커맨드가 존재하지 않을 때*/
 	 	
 	 			$this->setCmd(strtolower($args[1]));
 	 			$sender->sendMessage("§b§o [ 알림 ] §7명령어 '".strtolower($args[1])."' 을(를) 잠금했습니다.");
 	 			return true;
 	 		}
 	 		#-----------------------------------------------------------------------------
 	 		if ($args[0] === "unlock") {
 	 			if (!(isset($args[1]))) {
 	 				$sender->sendMessage($msg);
 	 				return true;
 	 			} //닉네임이 없을 때
 	 	
 	 			/*if ($this->getServer()->getCommandMap()->getCommand(strtolower($args[1])) === null) {
 	 				$sender->sendMessage("§b§o [ 알림 ] §7명령어가 존재하지 않습니다.");
 	 				return true;
 	 			}*/
 	 	
 	 			if (!($this->isCmd(strtolower($args[1])))) {
 	 				$sender->sendMessage("§b§o [ 알림 ] §7이 명령어는 등록되어 있지 않습니다.");
 	 				return true;
 	 			} //닉네임이 경찰이 아닐 때
 	 	
 	 			$this->removeCmd(strtolower($args[1]));
 	 			$sender->sendMessage("§b§o [ 알림 ] §7 명령어 '".$args[1]."' 의 잠금을 해제했습니다.");
 	 			return true;
 	 		}
 	 		#-----------------------------------------------------------------------------
 	 		if ($args[0] === "list") {
 	 			$list = implode(", ", $this->getCmd());
 	 			$sender->sendMessage("§b§o [ 알림 ] §7잠금된 명령어 목록 : " . $list);
 	 			return true; //리스트
 	 			#-----------------------------------------------------------------------------
 	 		}else{
 	 			$sender->sendMessage($msg);
 	 			return true; //$args[0]가 존재하지 않을 때
 	 			#-----------------------------------------------------------------------------
 	 		}
 	 	}
 	 }
 	 
 	 public function onCommandProcessEvent (PlayerCommandPreprocessEvent $ev) {
 	 	
 	 	$player = $ev->getPlayer();
 	 	$msg = $ev->getMessage();
 	 		
 	 	$words = explode(" ", $msg);
 	 		
 	 	$cmd = strtolower(substr(array_shift($words), 1));
 	 	
 	 	if ($this->isCmd($cmd)) {	
 	 		if (!($player->isOp())) {
				if(!($this->getServer()->getCommandMap()->getCommand($cmd) === null)) {
					$player->sendMessage("§b§o[ 알림 ] §7 이 명령어는 잠겨 있습니다.");
					$ev->setCancelled(true);
				}
 	 		}
 	 	}
 	 	
 	 }
  }
?>

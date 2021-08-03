<?php
namespace Yaser\iphone;

use pocketmine\plugin\PluginBase;

use pocketmine\plugin\Plugin;

use pocketmine\Server;

use pocketmine\Player;

use pocketmine\command\Command;

use pocketmine\command\CommandSender;

use pocketmine\utils\TextFormat;

use pocketmine\event\player\PlayerInteractEvent;

use pocketmine\event\Listener;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\item\Item;
use onebone\economyapi\EconomyAPI;


class Main extends PluginBase implements Listener{
#Start
    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TextFormat::GREEN . "Plugin Enabled");
    }
    public function onDisable()
    {
        $this->getLogger()->info(TextFormat::RED . "Plugin Disabled");
    }
    public function onCommand(\pocketmine\command\CommandSender $player, \pocketmine\command\Command $command, string $label, array $args): bool
    {
        if($command->getName() == "iphone"){
            if($player instanceof Player){
                $this->buy($player);
            }else{
                $player->sendMessage("§2[ Iphone ] §cUse This Command In Game");
            }

        }

        return true;
    }
    public function buy($player){
        $api= $this->getServer()->getPluginManager()->getPlugin("FormAPI"); 
        $form = $api->createSimpleForm(function (Player $player, $data = null){
            $result = $data;
            if($result === null){
                return true;
            }
            switch($result){ 
                case 0:
                $economy = $this->getServer()->getPluginManager()->getPlugin('EconomyAPI');
			    if ($economy->myMoney($player) >= 50000) {
					EconomyAPI::getInstance()->reduceMoney($player,50000);	
                    $server = $this->getServer();
                    $this->panel($player);
					$player->sendMessage("§2[ Iphone] §aYou successfully Buyed Phone");
					
				}else{
					$player->sendMessage("§2[ Iphone] §cYou Dont Have Money");
				}
				
                break;
				case 1:
					$player->sendMessage("§2[ Iphone] §eI will Add New Phones In New Update");
                break;

            }
        });
        $form->setTitle("§aPhone Shop");
        $form->setContent("§eSelect Your Phone");
        $form->addButton("§8Iphone 12");
		$form->addButton("Soon");
        $form->sendToPlayer($player);    
        return $form;
    }
    public function panel($player){
		
        $phone = Item::get(Item::PAPER);
	    $phone->setCustomName("§2Iphone12");
        $player->getInventory()->addItem($phone);
    }
    public function oninteract(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $item = $event->getItem();
        if ($player->getInventory()->getItemInHand()->getCustomName() === "§2Iphone12") {
			$this->openpanel($player);
		}
	}
				
	public function openpanel($player){
        $api= $this->getServer()->getPluginManager()->getPlugin("FormAPI"); 
        $form = $api->createSimpleForm(function (Player $player, $data = null){
            $result = $data;
            if($result === null){
                return true;
            }
            switch($result){ 
                case 0:
                    $this->pay($player);
				
                break;
				case 1:
					$this->charity($player);
                break;
				case 2:
					$this->message($player);
                break;

            }
        });
        $form->setTitle("§2Phone 12");
        $form->setContent("§eSelect Your Menu");
        $form->addButton("§8Online Pay");
		$form->addButton("§8Help charity");
		$form->addButton("§8Send Message");
        $form->sendToPlayer($player);    
        return $form;
    }
	public function pay(player $player){
		$api = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
		
		$form = $api->createCustomForm(function(player $player, array $data = null){
			

			if($data === null){
				return true;
			}
		    $target = $this->getServer()->getPlayer($data[1]);
		    if ($target !== null){
			    if(strtolower($data[1]) === strtolower($player->getName())){
				    $player->sendMessage("§cYou Cant Pay Your Self");
				    return;
			    }
			    if($data === null){
			    }
		        if(!is_numeric($data[2])){
				    $player->sendMessage("§cPlease Use Number");
				    return;
			    }
			
			    if($data[2] <= 0){
				    $player->sendMessage("§cDont Use - Or...");
				    return;
			    }
				$namet = $target->getName();
				$name = $player->getName();
                $economy = $this->getServer()->getPluginManager()->getPlugin('EconomyAPI');
			    if ($economy->myMoney($player) >= $data[2]) {
					EconomyAPI::getInstance()->reduceMoney($player,$data[2]);	
			        $this->getServer()->dispatchCommand(new ConsoleCommandSender(), "givemoney $data[1] $data[2]");
				    $player->sendMessage("§aYou Payed $data[1] Money: $data[2]");
					$target->sendMessage("§2$name Payed You: $data[2]");
				}else{
					$player->sendMessage("§cYou Dont Have Money");
				}
			}else{
				$player->sendMessage("§cPlayer $data[1] Is Not Online");
			}
			
		 
		});	

        $form->setTitle("§bPay");
        $form->addLabel("§6How Much Money Wants To Pay?");
		$form->addInput("Player Name", "Type here...");
		$form->addInput("Money", "Type here...");
        $form->sendToPlayer($player);
		
		return $form;
	}
	public function charity(player $player){
		$api = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
		
		$form = $api->createCustomForm(function(player $player, array $data = null){
			

			if($data === null){
				return true;
			}
			    if($data === null){
			    }
		        if(!is_numeric($data[1])){
				    $player->sendMessage("§cPlease Use Number");
				    return;
			    }
			
			    if($data[1] <= 0){
				    $player->sendMessage("§cDont Use - Or...");
				    return;
			    }
				$name = $player->getName();
                $economy = $this->getServer()->getPluginManager()->getPlugin('EconomyAPI');
			    if ($economy->myMoney($player) >= $data[1]) {
					EconomyAPI::getInstance()->reduceMoney($player,$data[1]);	
				    $player->sendMessage("§aYou Helped Charity Money: $data[1]");
				}else{
					$player->sendMessage("§cYou Dont Have Money");
				}
			
		 
		});	

        $form->setTitle("§bCharity");
        $form->addLabel("§6How Much Money Wants To Help?");
		$form->addInput("Money", "Type here...");
        $form->sendToPlayer($player);
		
		return $form;
	}
	public function message(player $player){
		$api = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
		
		$form = $api->createCustomForm(function(player $player, array $data = null){
			

			if($data === null){
				return true;
			}
		    $target = $this->getServer()->getPlayer($data[1]);
		    if ($target !== null){
			    if(strtolower($data[1]) === strtolower($player->getName())){
				    $player->sendMessage("§cYou Cant Message Your Self");
				    return;
			    }
			    if($data === null){
			    }
			
				$namet = $target->getName();
				$name = $player->getName();
			        $this->getServer()->dispatchCommand(new ConsoleCommandSender(), "tell $data[1] §aPlayer $name Send You Message: §b $data[2]");
				    $player->sendMessage("§aYou successfully Send Message For $data[1]");
			}else{
				$player->sendMessage("§cPlayer $data[1] Is Not Online");
			}
			
		 
		});	

        $form->setTitle("§bMessage");
        $form->addLabel("§6Send Message For Players");
		$form->addInput("Player Name", "Type here...");
		$form->addInput("Message", "Type here...");
        $form->sendToPlayer($player);
		
		return $form;
	}
	
}
//Done
//Thanks For Using My Plugin
//Star Plugin Please

<?php

  namespace LaggyKick;

  use pocketmine\plugin\PluginBase;
  use pocketmine\event\player\PlayerJoinEvent;
  use pocketmine\utils\TextFormat as TF;
  use pocketmine\utils\Config;
  use pocketmine\command\Command;
  use pocketmine\command\CommandSender;

  class Main extends PluginBase implements Listener {

    public function dataPath() {

      return $this->getDataFolder();

    }

    public function ping($host, $port = 80, $timeout = 3) {

      $tB = microtime(true);

      $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);

      if(!($socket)) {

        $this->getServer()->getLogger()->error("Could not open socket on $host:$port");

      }

      $tA = microtime(true);

      return round((($tA - $tB) * 1000), 0) . " ms";

    }

    public function onEnable() {

      @mkdir($this->dataPath());

      $this->cfg = new Config($this->dataPath() . "config.yml", Config::YAML, array("max_ping" => "200", "kick_message" => "Your ping was too high."));

    }

    public function onJoin(PlayerJoinEvent $event) {

      $player = $event->getPlayer();

      $player_name = $player->getName();

      $player_address = $player->getAddress();

      $ping_results = ping($player_address);

      $max_ping = $this->cfg->get("max_ping");

      $kick_message = $this->cfg->get("kick_message");

      if((int)$ping_results <= $max_ping) {

        $player->kick(TF::GREEN . $kick_message, false);

      }

    }

    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {

      if(strtolower($cmd->getName()) === "ping") {

        if(!(isset($args[0]))) {

          $sender->sendMessage(TF::RED . "Error: not enough args. Usage: /ping <player>");

          return true;

        } else {

          $player = $this->getServer()->getPlayer($args[0]);

          if($player === null) {

            $sender->sendMessage(TF::RED . "Player " . $args[0] . " could not be found.");

            return true;

          } else {

            $player_name = $player->getName();

            $player_address = $player->getAddress();

            $sender->sendMessage(TF::GREEN . "Pinging " . $player_name . "...");

            $ping_results = ping($player_address);

            $sender->sendMessage(TF::GREEN . "Ping Results: " . $ping_results);

            return true;

          }

        }

      }

    }

  }

?>

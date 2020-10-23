<?php

/*
 *
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the MIT License. see <https://opensource.org/licenses/MIT>.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://opensource.org/licenses/MIT MIT License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace blugin\lib\command\enum;

use blugin\traits\singleton\SingletonTrait;
use blugin\utils\arrays\ArrayUtil as Arr;
use blugin\utils\string\StringUtil as Str;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\Server;

class EnumFactory{
    use SingletonTrait;

    /** @var Enum[] name => enum */
    protected $enums = [];

    private function __construct(){
        $server = Server::getInstance();
        $this->set(Enum::BOOLEAN, ["true" => true, "false" => false]);
        $this->set(Enum::PLAYERS, $players = Arr::keyMapAs($server->getOnlinePlayers(), function(Player $player) : string{ return strtolower($player->getName()); }));

        $this->set(Enum::PLAYERS_INCLUE_OFFLINE,
            Arr::from($players)
                ->map(function(Player $player){ return $player->getName(); })
                ->mergeSoftAs(
                    Arr::from(scandir($server->getDataPath() . "players/"))
                        ->filter(function(string $fileName) : bool{ return Str::endsWith($fileName, ".dat"); })
                        ->map([Str::class, "removeExtension"])
                        ->combine()
                )
        );

        $this->set(Enum::WORLDS, Arr::keyMapAs($server->getLevels(), function(Level $world) : string{ return strtolower($world->getFolderName()); }));
    }

    /** @return Enum[] */
    public function getAll() : array{
        return $this->enums;
    }

    /** @param mixed[]|null $values name => value */
    public function get(string $name) : ?Enum{
        return $this->enums[$name] ?? null;
    }

    /** @param mixed[]|null $values name => value */
    public function set(string $name, array $values = []) : Enum{
        if(!isset($this->enums[$name])){
            $this->enums[$name] = new Enum($name, $values);
        }elseif($values !== null && $this->enums[$name]->getAll() !== $values){
            $this->enums[$name]->setAll($values);
        }
        return $this->enums[$name];
    }
}
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

namespace kim\present\lib\command\enum;

use kim\present\lib\arrayutils\ArrayUtils as Arr;
use kim\present\lib\stringutils\StringUtils as Str;
use pocketmine\network\mcpe\protocol\types\CommandEnum;
use pocketmine\Server;

class Enum extends CommandEnum{
    public const BOOLEAN = "bool";
    public const PLAYERS = "target";
    public const PLAYERS_INCLUE_OFFLINE = "allplayer";
    public const WORLDS = "worlds";

    /** @var mixed[] name => value */
    protected $values;

    /** @param mixed[]|null $values name => value */
    public function __construct(string $name, ?array $values = null){
        $this->enumName = $name;
        $this->values = $values ?? [];
        $this->enumValues = $this->getValues();
    }

    public function getName() : string{
        return $this->enumName;
    }

    /** @return string[] name[] */
    public function getValues() : array{
        return Arr::keysFrom($this->values)
            ->mapAs(function($value){
                return is_string($value) && Str::contains((string) $value, " ") ? "\"$value\"" : $value;
            });
    }

    /** @return mixed[] name => value */
    public function getAll(){
        return $this->values;
    }

    /** @param mixed[] $values name => value */
    public function setAll(array $values) : Enum{
        $this->values = [];
        foreach($values as $name => $value){
            $this->values[(string) $name] = $value;
        }
        $this->onUpdate();
        return $this;
    }

    public function has(string $name) : bool{
        return isset($this->values[$name]);
    }

    /** @return mixed|null */
    public function get(string $name){
        return $this->values[$name] ?? null;
    }

    public function set(string $name, $value) : Enum{
        if(!isset($this->values[$name]) || $this->values[$name] !== $value){
            $this->values[$name] = $value;
            $this->onUpdate();
        }
        return $this;
    }

    public function remove(string $name) : Enum{
        if(isset($this->values[$name])){
            unset($this->values[$name]);
            $this->onUpdate();
        }
        return $this;
    }

    protected function onUpdate() : void{
        /*
         * TODO: Figure out how to use softEnums
        */
        $this->enumValues = $this->getValues();
        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            $player->sendCommandData();
        }
    }
}
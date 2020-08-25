<?php

/*
 *
 *  ____  _             _         _____
 * | __ )| |_   _  __ _(_)_ __   |_   _|__  __ _ _ __ ___
 * |  _ \| | | | |/ _` | | '_ \    | |/ _ \/ _` | '_ ` _ \
 * | |_) | | |_| | (_| | | | | |   | |  __/ (_| | | | | | |
 * |____/|_|\__,_|\__, |_|_| |_|   |_|\___|\__,_|_| |_| |_|
 *                |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  Blugin team
 * @link    https://github.com/Blugin
 * @license https://www.gnu.org/licenses/lgpl-3.0 LGPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace blugin\lib\command;

use blugin\lib\command\parameter\additions\ConstParameter;
use blugin\lib\command\parameter\Parameter;
use blugin\lib\translator\TranslatorHolder;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\TranslationContainer;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class BaseCommand extends Command implements PluginOwned{
    use PluginOwnedTrait;

    /** @var Overload[] */
    private $overloads = [];

    public function __construct(string $name, PluginBase $owner){
        parent::__construct($name);
        $this->owningPlugin = $owner;

        if($owner instanceof TranslatorHolder){
            $label = strtolower($owner->getName());
            $this->setDescription($owner->getTranslator()->translate("commands.$label.description"));
        }
    }

    /** @param string[] $args */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        if(!$this->owningPlugin->isEnabled() || !$this->testPermission($sender))
            return false;

        if(empty($this->overloads))
            return false;

        foreach($this->overloads as $key => $overload){
            if($overload->valid($sender, $args)){
                $result = $overload->parse($sender, $args);
                switch($result){
                    case Overload::ERROR_NAME_MISMATCH:
                        break;
                    case Overload::ERROR_PARAMETER_INVALID:
                    case Overload::ERROR_PARAMETER_INSUFFICIENT:
                        $this->sendMessage($sender, "commands.generic.usage", ["/{$this->getName()} " . $overload->toUsageString()]);
                        return true;
                    case Overload::ERROR_PERMISSION_DENIED:
                        $this->sendMessage($sender, TextFormat::RED . "%commands.generic.permission");
                        return true;
                    default:
                        return is_numeric($result) ? true : $overload->onParse($sender, $result);
                }
            }
        }
        $this->sendMessage($sender, "commands.generic.usage", [$this->getUsage()]);
        return true;
    }

    public function getUsage() : string{
        $usage = "/{$this->getName()}";

        $count = count($this->overloads);
        if($count === 0)
            return $usage;

        if($count === 1)
            return "$usage {$this->overloads[0]->toUsageString()}";

        return "$usage <" . implode(" | ", array_map(function(Overload $overload) : string{
                return $overload->getName() ?? $overload->toUsageString();
            }, $this->overloads)) . ">";
    }

    public function getMessage(CommandSender $sender, string $str, array $params = []) : string{
        if($this->owningPlugin instanceof TranslatorHolder){
            $str = $this->owningPlugin->getTranslator()->translateTo($str, $params, $sender);
        }

        return Server::getInstance()->getLanguage()->translateString($str, $params);
    }

    /** @param string[] $params */
    public function sendMessage(CommandSender $sender, string $str, array $params = []) : void{
        $sender->sendMessage(new TranslationContainer($this->getMessage($sender, $str, $params), $params));
    }

    /** @return Overload[] */
    public function getOverloads() : array{
        return $this->overloads;
    }

    public function addOverload(Overload $overload) : void{
        $this->overloads[] = $overload;
    }

    /**
     * @return Parameter[][]
     */
    public function asOverloadsArray() : array{
        $overloads = [];
        foreach($this->overloads as $overload){
            $parameters = $overload->getParameters();
            $name = $overload->getName();
            if($name !== null){
                array_unshift($parameters, new ConstParameter($overload, $name));
            }
            $overloads[] = $parameters;
        }
        return $overloads;
    }
}
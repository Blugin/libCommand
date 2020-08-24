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

use blugin\lib\translator\TranslatorHolder;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\TranslationContainer;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\Server;

class BaseCommand extends Command implements PluginOwned{
    use PluginOwnedTrait;

    /** @var ParameterLine[] */
    private $parameterLines = [];

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

        if(empty($this->parameterLines))
            return false;

        foreach($this->parameterLines as $key => $parameterLine){
            if($parameterLine->valid($sender, $args)){
                $result = $parameterLine->parse($sender, $args);
                return is_numeric($result) ? true : $parameterLine->onParse($sender, $result);
            }
        }
        $sender->sendMessage(Server::getInstance()->getLanguage()->translateString("commands.generic.usage", [$this->getUsage()]));
        return true;
    }

    public function getUsage() : string{
        $usage = "/{$this->getName()}";

        $count = count($this->parameterLines);
        if($count === 0)
            return $usage;

        if($count === 1)
            return "$usage {$this->parameterLines[0]->toUsageString()}";

        return "$usage <" . implode(" | ", array_map(function(ParameterLine $parameterLine) : string{
                return $parameterLine->getName() ?? $parameterLine->toUsageString();
            }, $this->parameterLines)) . ">";
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

    /** @return ParameterLine[] */
    public function getParameterLines() : array{
        return $this->parameterLines;
    }

    public function addParameterLine(ParameterLine $parameterLine) : void{
        $this->parameterLines[] = $parameterLine;
    }
}
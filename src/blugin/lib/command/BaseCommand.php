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

use blugin\lib\command\exception\ExceptionHandler;
use blugin\lib\translator\TranslatorHolder;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class BaseCommand extends Command implements CommandExecutor{
    /** @var PluginBase */
    private $owningPlugin;

    /** @var Subcommand[] */
    private $subcommands = [];

    /** @var ExceptionHandler */
    private $exceptionHandler;

    public function __construct(string $name, PluginBase $owner){
        parent::__construct($name);
        $this->owningPlugin = $owner;
        $this->exceptionHandler = new ExceptionHandler($this);

        if($owner instanceof TranslatorHolder){
            $label = strtolower($owner->getName());
            $this->setUsage($owner->getTranslator()->translate("commands.$label.usage"));
            $this->setDescription($owner->getTranslator()->translate("commands.$label.description"));
        }
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
        return $this->owningPlugin->isEnabled() && $this->testPermission($sender) && $this->onCommand($sender, $this, $commandLabel, $args);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        $label = array_shift($args) ?? "";
        foreach($this->subcommands as $key => $subcommand){
            if($subcommand->checkLabel($label)){
                try{
                    $subcommand->handle($sender, $args);
                }catch(\Exception $e){
                    if(!$this->exceptionHandler->handle($e, $sender, $subcommand))
                        throw $e;
                }
                return true;
            }
        }
        $sender->sendMessage(Server::getInstance()->getLanguage()->translateString("commands.generic.usage", [$this->getUsage($sender)]));
        return true;
    }

    /**
     * Override for display different usage messages depending on player permissions
     */
    public function getUsage(CommandSender $sender = null) : string{
        if($sender === null || !$this->owningPlugin instanceof TranslatorHolder)
            return $this->usageMessage;

        $subCommands = [];
        foreach($this->subcommands as $key => $subCommand){
            if($subCommand->testPermissionSilent($sender)){
                $subCommands[] = $subCommand->getName();
            }
        }
        $label = strtolower($this->getName());
        return $this->getMessage($sender, "commands.$label.usage", [implode(" | ", $subCommands)]);
    }

    public function getMessage(CommandSender $sender, string $str, array $params = []) : string{
        if($this->owningPlugin instanceof TranslatorHolder){
            return $this->owningPlugin->getTranslator()->translateTo($str, $params, $sender);
        }

        return Server::getInstance()->getLanguage()->translateString($str, $params);
    }

    public function sendMessage(CommandSender $sender, string $str, array $params = []) : void{
        $sender->sendMessage($this->getMessage($sender, $str, $params));
    }

    /** @return Subcommand[] */
    public function getSubcommands() : array{
        return $this->subcommands;
    }

    public function registerSubcommand(Subcommand $subcommand) : void{
        $this->subcommands[$subcommand->getLabel()] = $subcommand;
    }

    public function unregisterSubcommand(string $label) : bool{
        if(isset($this->subcommands[$label])){
            unset($this->subcommands[$label]);
            return true;
        }
        return false;
    }

    public function getExceptionHandler() : ExceptionHandler{
        return $this->exceptionHandler;
    }

    /** @return PluginBase */
    public function getOwningPlugin() : PluginBase{
        return $this->owningPlugin;
    }
}
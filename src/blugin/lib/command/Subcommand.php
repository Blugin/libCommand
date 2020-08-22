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
 * it under the terms of the MIT License.
 *
 * @author  Blugin team
 * @link    https://github.com/Blugin
 * @license https://www.gnu.org/licenses/mit MIT License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace blugin\lib\command;

use blugin\lib\command\exception\defaults\ArgumentLackException;
use blugin\lib\translator\TranslatorHolder;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\utils\TextFormat;

abstract class Subcommand{
    /** @var BaseCommand */
    private $baseCommand;

    /** @var string */
    private $name;

    /** @var string[] */
    private $aliases;

    /**
     * @param BaseCommand   $baseCommand
     * @param null|string   $name = null
     * @param null|string[] $aliases = null
     */
    public function __construct(BaseCommand $baseCommand, ?string $name = null, ?array $aliases = null){
        $this->baseCommand = $baseCommand;

        $label = $this->getLabel();
        $config = $baseCommand->getOwningPlugin()->getConfig();
        $this->name = $name ?? $config->getNested("command.children.$label.name", $label);
        $this->aliases = $aliases ?? $config->getNested("command.children.$label.aliases", []);

        $permissionManager = PermissionManager::getInstance();
        $permissionManager->addPermission(new Permission($this->getPermission(), $this->baseCommand->getUsage(), $permissionManager->getPermission($baseCommand->getPermission())->getDefault()));
    }

    public function handle(CommandSender $sender, array $args = []) : bool{
        if(!$this->testPermission($sender) || $this->execute($sender, $args))
            return true;

        throw new ArgumentLackException();
    }

    /**
     * @param string[] $params
     */
    public function sendMessage(CommandSender $sender, string $str, array $params = []) : void{
        $sender->sendMessage($this->getBaseCommand()->getMessage($sender, $this->getFullMessage($str), $params));
    }

    public function testPermission(CommandSender $sender) : bool{
        if($this->testPermissionSilent($sender))
            return true;

        $this->baseCommand->sendMessage($sender, TextFormat::RED . "%commands.generic.permission");
        return false;
    }

    public function testPermissionSilent(CommandSender $sender) : bool{
        return $sender->hasPermission($this->getPermission());
    }

    public function checkLabel(string $label) : bool{
        return strcasecmp($label, $this->name) === 0 || in_array($label, $this->aliases);
    }

    public function getBaseCommand() : BaseCommand{
        return $this->baseCommand;
    }

    public function getName() : string{
        return $this->name;
    }

    public function setName(string $name) : void{
        $this->name = $name;
    }

    /**
     * @return string[]
     */
    public function getAliases() : array{
        return $this->aliases;
    }

    /**
     * @param string[] $aliases
     */
    public function setAliases(array $aliases) : void{
        $this->aliases = $aliases;
    }

    public function getPermission() : string{
        return $this->baseCommand->getPermission() . "." . $this->getLabel();
    }

    public function getUsage(?CommandSender $sender = null) : string{
        $plugin = $this->baseCommand->getOwningPlugin();
        if($plugin instanceof TranslatorHolder){
            return $plugin->getTranslator()->translateTo($this->getFullMessage("usage"), [], $sender);
        }
        return "";
    }

    public function getFullMessage(string $str) : string{
        $label = strtolower($this->baseCommand->getName());
        return "commands.$label.{$this->getLabel()}.$str";
    }

    /**
     * @param string[] $args
     */
    public abstract function execute(CommandSender $sender, array $args = []) : bool;

    public abstract function getLabel() : string;
}
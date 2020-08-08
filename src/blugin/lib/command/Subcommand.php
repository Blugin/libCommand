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

use blugin\lib\command\exception\defaults\ArgumentLackException;
use blugin\lib\translator\TranslatorHolder;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\utils\TextFormat;

abstract class Subcommand{
    /** @var MainCommand */
    private $mainCommand;

    /** @var string */
    private $name;

    /** @var string[] */
    private $aliases;

    /**
     * @param MainCommand $mainCommand
     * @param null|string $name = null
     * @param null|array  $aliases = null
     */
    public function __construct(MainCommand $mainCommand, ?string $name = null, ?array $aliases = null){
        $this->mainCommand = $mainCommand;

        $label = $this->getLabel();
        $config = $mainCommand->getOwningPlugin()->getConfig();
        $this->name = $name ?? $config->getNested("command.children.$label.name", $label);
        $this->aliases = $aliases ?? $config->getNested("command.children.$label.aliases", []);

        $permissionManager = PermissionManager::getInstance();
        $permissionManager->addPermission(new Permission($this->getPermission(), $this->mainCommand->getUsage(), $permissionManager->getPermission($mainCommand->getPermission())->getDefault()));
    }

    /**
     * @param CommandSender $sender
     * @param string[]      $args = []
     *
     * @return bool
     */
    public function handle(CommandSender $sender, array $args = []) : bool{
        if(!$this->testPermission($sender) || $this->execute($sender, $args))
            return true;

        throw new ArgumentLackException();
    }

    /**
     * @param CommandSender          $sender
     * @param string                 $str
     * @param float[]|int[]|string[] $params
     */
    public function sendMessage(CommandSender $sender, string $str, array $params = []) : void{
        $sender->sendMessage($this->getMainCommand()->getMessage($sender, $this->getFullMessage($str), $params));
    }

    /**
     * @param CommandSender $sender
     *
     * @return bool
     */
    public function testPermission(CommandSender $sender) : bool{
        if($this->testPermissionSilent($sender))
            return true;

        $this->mainCommand->sendMessage($sender, TextFormat::RED . "%commands.generic.permission");
        return false;
    }

    /**
     * @param CommandSender $sender
     *
     * @return bool
     */
    public function testPermissionSilent(CommandSender $sender) : bool{
        return $sender->hasPermission($this->getPermission());
    }

    /**
     * @param string $label
     *
     * @return bool
     */
    public function checkLabel(string $label) : bool{
        return strcasecmp($label, $this->name) === 0 || in_array($label, $this->aliases);
    }

    /** @return MainCommand */
    public function getMainCommand() : MainCommand{
        return $this->mainCommand;
    }

    /** @return string */
    public function getName() : string{
        return $this->name;
    }

    /** @param string $name */
    public function setName(string $name) : void{
        $this->name = $name;
    }

    /** @return string[] */
    public function getAliases() : array{
        return $this->aliases;
    }

    /** @param string[] $aliases */
    public function setAliases(array $aliases) : void{
        $this->aliases = $aliases;
    }

    /** @return string */
    public function getPermission() : string{
        return $this->mainCommand->getPermission() . "." . $this->getLabel();
    }

    /**
     * @param CommandSender|null $sender
     *
     * @return string
     */
    public function getUsage(CommandSender $sender = null) : string{
        $plugin = $this->mainCommand->getOwningPlugin();
        if($plugin instanceof TranslatorHolder){
            return $plugin->getTranslator()->translateTo($this->getFullMessage("usage"), [], $sender);
        }
        return "";
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public function getFullMessage(string $str) : string{
        $label = strtolower($this->mainCommand->getName());
        return "commands.$label.{$this->getLabel()}.$str";
    }

    /**
     * @param CommandSender $sender
     * @param string[]      $args = []
     *
     * @return bool
     */
    public abstract function execute(CommandSender $sender, array $args = []) : bool;

    /** @return string */
    public abstract function getLabel() : string;
}
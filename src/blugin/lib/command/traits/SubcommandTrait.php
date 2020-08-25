<?php /** @noinspection PhpParamsInspection */

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

namespace blugin\lib\command\traits;

use blugin\lib\command\BaseCommand;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

/**
 * This trait override most methods in the {@link PluginBase} abstract class.
 */
trait SubcommandTrait{
    /** @var BaseCommand */
    private $baseCommand;

    public function getBaseCommand(?string $label = null) : BaseCommand{
        if($this->baseCommand === null){
            $this->baseCommand = $this->createCommand($label);
        }

        return $this->baseCommand;
    }

    public function createCommand(?string $label = null) : BaseCommand{
        return new BaseCommand(trim(strtolower($label ?? $this->getName())), $this, $this->getConfig()->getNested("command.aliases", []));
    }

    public function recalculatePermissions() : void{
        $permissionManager = PermissionManager::getInstance();
        $config = $this->getConfig();

        $defaultValue = $config->getNested("command.permission");
        if($defaultValue !== null){
            $permissionManager->getPermission($this->baseCommand->getPermission())->setDefault($defaultValue);
        }
        foreach($this->baseCommand->getOverloads() as $key => $overload){
            $name = $overload->getName();
            if($name === null)
                continue;

            $label = strtolower($name);
            $defaultValue = $config->getNested("command.children.$label.permission");
            if($defaultValue !== null){
                $permissionManager->getPermission($overload->getPermission())->setDefault($defaultValue);
            }
        }
    }

    public function onLoad() : void{
        $this->getBaseCommand();
    }

    public function onEnable() : void{
        Server::getInstance()->getCommandMap()->register($this->getName(), $this->getBaseCommand());
    }

    public function onDisble() : void{
        Server::getInstance()->getCommandMap()->unregister($this->getBaseCommand());
    }
}
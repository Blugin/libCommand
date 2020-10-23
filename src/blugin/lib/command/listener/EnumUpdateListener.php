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

namespace blugin\lib\command\listener;

use blugin\lib\command\enum\Enum;
use blugin\lib\command\enum\EnumFactory;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\level\LevelUnloadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class EnumUpdateListener implements Listener{
    use ListenerTrait;

    /**
     * @priority MONITOR
     */
    public function onPlayerJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        EnumFactory::getInstance()->get(Enum::PLAYERS)->set(strtolower($player->getName()), $player);
    }

    /**
     * @priority MONITOR
     */
    public function onPlayerQuit(PlayerQuitEvent $event) : void{
        $player = $event->getPlayer();
        EnumFactory::getInstance()->get(Enum::PLAYERS)->remove(strtolower($player->getName()));
    }

    /**
     * @priority MONITOR
     */
    public function onWorldLoad(LevelLoadEvent $event) : void{
        $world = $event->getLevel();
        EnumFactory::getInstance()->get(Enum::WORLDS)->set(strtolower($world->getFolderName()), $world);
    }

    /**
     * @priority MONITOR
     */
    public function onWorldUnload(LevelUnloadEvent $event) : void{
        $world = $event->getLevel();
        EnumFactory::getInstance()->get(Enum::WORLDS)->remove(strtolower($world->getFolderName()));
    }
}

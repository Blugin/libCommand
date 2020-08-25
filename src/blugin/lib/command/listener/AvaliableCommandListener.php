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

namespace blugin\lib\command\listener;

use blugin\lib\command\BaseCommand;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\Server;

class AvaliableCommandListener implements Listener{
    /**
     * @param DataPacketSendEvent $event
     *
     * @priority HIGHEST
     */
    public function onDataPacketSend(DataPacketSendEvent $event) : void{
        $packets = $event->getPackets();
        foreach($packets as $packet){
            if($packet instanceof AvailableCommandsPacket){
                foreach($packet->commandData as $name => $commandData){
                    $command = Server::getInstance()->getCommandMap()->getCommand($name);
                    if($command instanceof BaseCommand){
                        $commandData->overloads = $command->asOverloadsArray();
                    }
                }
            }
        }
    }
}

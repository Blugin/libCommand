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

use blugin\lib\command\BaseCommand;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\Server;

class AvaliableCommandListener implements Listener{
    use ListenerTrait;

    /**
     * @priority HIGHEST
     */
    public function onDataPacketSend(DataPacketSendEvent $event) : void{
        $packet = $event->getPacket();
        if($packet instanceof AvailableCommandsPacket){
            foreach($packet->commandData as $name => $commandData){
                $command = Server::getInstance()->getCommandMap()->getCommand($name);
                if($command instanceof BaseCommand){
                    $commandData->overloads = $command->asOverloadsArray($event->getPlayer());
                }
            }
        }
    }
}

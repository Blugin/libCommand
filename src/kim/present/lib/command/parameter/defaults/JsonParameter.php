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

namespace kim\present\lib\command\parameter\defaults;

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class JsonParameter extends TextParameter{
    public function getType() : int{
        return AvailableCommandsPacket::ARG_TYPE_JSON;
    }

    public function getTypeName() : string{
        return "json";
    }

    public function valid(CommandSender $sender, string $argument) : bool{
        return preg_match("/^{(.*)}$/", $argument);
    }

    /** @return mixed[]|null the parsed json array */
    public function parseSilent(CommandSender $sender, string $argument){
        $data = json_decode($argument, true);
        return $data === null || !is_array($data) ? null : $data;
    }
}
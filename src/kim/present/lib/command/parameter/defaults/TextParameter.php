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

use kim\present\lib\command\overload\Overload;
use kim\present\lib\command\parameter\Parameter;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class TextParameter extends Parameter{
    public function getType() : int{
        return AvailableCommandsPacket::ARG_TYPE_RAWTEXT;
    }

    public function getTypeName() : string{
        return "text";
    }

    public function prepare(Overload $overload, ?CommandSender $sender = null) : Parameter{
        $this->setLength(PHP_INT_MAX);
        return parent::prepare($overload, $sender);
    }
}
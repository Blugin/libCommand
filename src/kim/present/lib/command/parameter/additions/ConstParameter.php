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

namespace kim\present\lib\command\parameter\additions;

use kim\present\lib\command\enum\EnumFactory;
use kim\present\lib\command\parameter\defaults\EnumParameter;
use kim\present\lib\command\parameter\Parameter;

class ConstParameter extends EnumParameter{
    public function getTypeName() : string{
        return $this->getName();
    }

    public function prepare() : Parameter{
        $this->enum = EnumFactory::getInstance()->set("const-{$this->getName()}", [$this->getName() => $this->getName()]);
        return $this;
    }
}
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

namespace blugin\lib\command\handler;

use blugin\lib\command\overload\Overload;
use pocketmine\command\CommandSender;
use pocketmine\utils\Utils;

class ClosureCommandHandler implements ICommandHandler{
    /** @var \Closure */
    protected $closure;

    public function __construct(\Closure $closure){
        Utils::validateCallableSignature(function(CommandSender $sender, array $args, Overload $overload) : bool{
            return true;
        }, $closure);

        $this->closure = $closure;
    }

    /**
     * @param mixed[] $args name => value
     */
    public function handle(CommandSender $sender, array $args, Overload $overload) : bool{
        return ($this->closure)($sender, $args, $overload);
    }
}
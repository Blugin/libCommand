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

namespace blugin\lib\command\constraint;

use blugin\lib\command\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\Utils;

class ClousreConstraint implements Constraint{
    /** @var \Closure */
    protected $testFunc;

    /** @var \Closure */
    protected $onFailureFunc;

    /**
     * ClousreConstraint constructor.
     *
     * @param \Closure $testFunc
     * @phpstan-param \Closure(CommandSender, BaseCommand, string[]) : bool $testFunc
     * @param \Closure $onFailureFunc
     * @phpstan-param \Closure(CommandSender, BaseCommand, string[]) : void $onFailureFunc
     */
    public function __construct(\Closure $testFunc, \Closure $onFailureFunc){
        Utils::validateCallableSignature(function(CommandSender $sender, BaseCommand $command, array $args) : bool{ return true; }, $testFunc);
        Utils::validateCallableSignature(function(CommandSender $sender, BaseCommand $command, array $args) : void{ }, $onFailureFunc);
        $this->testFunc = $testFunc;
        $this->onFailureFunc = $onFailureFunc;
    }

    /** @param string[] $args */
    public function test(CommandSender $sender, BaseCommand $command, array $args) : bool{
        return ($this->testFunc)($sender, $command, $args);
    }

    /** @param string[] $args */
    public function onFailure(CommandSender $sender, BaseCommand $command, array $args) : void{
        ($this->onFailureFunc)($sender, $command, $args);
    }
}
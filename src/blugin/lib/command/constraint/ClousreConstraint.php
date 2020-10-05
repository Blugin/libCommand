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
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

namespace blugin\lib\command\exception\defaults;

use blugin\lib\command\exception\IHandleable;
use blugin\lib\command\BaseCommand;
use blugin\lib\command\Subcommand;
use pocketmine\command\CommandSender;

abstract class GenericArgumentException extends \InvalidArgumentException implements IHandleable{
    const LABEL = "";

    public static function getHandler() : \Closure{
        return function(GenericArgumentException $e, CommandSender $sender, Subcommand $subcommand, BaseCommand $mainCommand) : void{
            $mainCommand->sendMessage($sender, "commands.generic." . $e::LABEL, $e->getArgs());
        };
    }

    /** @var string[] */
    protected $args = [];

    /** @param string ...$args */
    public function __construct(...$args){
        parent::__construct("generic argument exception");
        $this->args = $args;
    }

    /** @return string[] */
    public function getArgs() : array{
        return $this->args;
    }
}
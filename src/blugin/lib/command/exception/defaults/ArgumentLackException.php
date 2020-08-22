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
use blugin\lib\command\Subcommand;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\Server;

class ArgumentLackException extends InvalidCommandSyntaxException implements IHandleable{
    public static function getHandler() : \Closure{
        return function(ArgumentLackException $e, CommandSender $sender, Subcommand $subcommand) : void{
            $sender->sendMessage(Server::getInstance()->getLanguage()->translateString("commands.generic.usage", [$subcommand->getUsage()]));
        };
    }
}
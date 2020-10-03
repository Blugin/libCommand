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

namespace blugin\lib\command\constraints;

use blugin\lib\command\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class InGameConstraint implements Constraint{
    /** @param string[] $args */
    public function test(CommandSender $sender, BaseCommand $command, array $args) : bool{
        return !$sender instanceof Player;
    }

    /** @param string[] $args */
    public function onFailure(CommandSender $sender, BaseCommand $command, array $args) : void{
        $command->sendMessage($sender, "commands.generic.onlyPlayer");
    }
}
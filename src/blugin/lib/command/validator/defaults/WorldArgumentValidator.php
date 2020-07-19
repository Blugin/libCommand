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
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  Blugin team
 * @link    https://github.com/Blugin
 * @license https://www.gnu.org/licenses/lgpl-3.0 LGPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace blugin\lib\command\validator\defaults;

use blugin\lib\command\exception\defaults\GenericInvalidWorldException;
use blugin\lib\command\validator\ArgumentValidator;
use pocketmine\Server;
use pocketmine\world\World;

class WorldArgumentValidator implements ArgumentValidator{
    /**
     * @param string $argument
     *
     * @return World
     *
     * @throw \Exception
     */
    public static function validate(string $argument) : World{
        $world = Server::getInstance()->getWorldManager()->getWorldByName($argument);
        if($world === null)
            throw new GenericInvalidWorldException($argument);

        return $world;
    }
}
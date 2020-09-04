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

namespace blugin\lib\command\listener;

use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;

/**
 * This trait for {@link Listener} interface.
 */
trait ListenerTrait{
    /** @var PLugin */
    private static $registrant = null;

    public static function isRegistered() : bool{
        return self::$registrant instanceof Plugin;
    }

    public static function register(Plugin $plugin) : void{
        if(self::isRegistered()){
            throw new \InvalidArgumentException("This event listener is already registered");
        }

        self::$registrant = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents(new self(), $plugin);
    }

    private function __construct(){}
}

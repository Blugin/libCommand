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

namespace kim\present\lib\command\config;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class CommandConfig extends Config{
    /** @var PluginBase */
    protected $owningPlugin;

    /** @var CommandConfigData[] name => command config data */
    protected $dataMap;

    public function __construct(PluginBase $owningPlugin){
        parent::__construct("{$owningPlugin->getDataFolder()}command.yml", self::YAML);
        $this->owningPlugin = $owningPlugin;
        $this->dataMap = CommandConfigData::parse($this->getAll());
    }

    /** @return CommandConfigData[] */
    public function getDataMap() : array{
        return $this->dataMap;
    }

    public function getData(string $name) : ?CommandConfigData{
        return $this->dataMap[$name] ?? null;
    }

    public function getOwningPlugin() : PluginBase{
        return $this->owningPlugin;
    }
}
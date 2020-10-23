<?php /** @noinspection PhpParamsInspection */

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

namespace blugin\lib\command\config;

use pocketmine\plugin\PluginBase;

/**
 * This trait override most methods in the {@link PluginBase} abstract class.
 */
trait CommandConfigTrait{
    /** @var CommandConfig */
    private $commandConfig = null;

    public function getCommandConfig() : CommandConfig{
        if($this->commandConfig === null){
            $this->loadCommandConfig();
        }
        return $this->commandConfig;
    }

    public function loadCommandConfig() : void{
        if(!$this->saveDefaultCommandConfig())
            throw new CommandConfigException("Default command configuration file not found");

        $this->commandConfig = new CommandConfig($this);
    }

    public function saveDefaultCommandConfig() : bool{
        $configFile = "{$this->getDataFolder()}command.yml";
        if(file_exists($configFile))
            return true;

        $resource = $this->getResource("command/{$this->getServer()->getLanguage()->getLang()}.yml");
        if($resource === null){
            foreach($this->getResources() as $filePath => $info){
                if(preg_match('/^command\/[a-zA-Z]{3}\.yml$/', $filePath)){
                    $resource = $this->getResource($filePath);
                    break;
                }
            }
        }
        if($resource === null)
            return false;

        $ret = stream_copy_to_stream($resource, $fp = fopen($configFile, "wb")) > 0;
        fclose($fp);
        fclose($resource);
        return $ret;
    }
}
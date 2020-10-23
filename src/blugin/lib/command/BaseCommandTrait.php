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

namespace blugin\lib\command;

use blugin\lib\command\config\CommandConfigTrait;
use pocketmine\plugin\PluginBase;

/**
 * This trait override most methods in the {@link PluginBase} abstract class.
 */
trait BaseCommandTrait{
    use CommandConfigTrait;

    /** @var BaseCommand */
    private $baseCommand = [];

    public function getBaseCommand(?string $label = null) : BaseCommand{
        if(!isset($this->baseCommand[$label = trim(strtolower($label ?? $this->getName()))])){
            $this->baseCommand[$label] = $this->createCommand($label);
        }

        return $this->baseCommand[$label];
    }

    public function createCommand(?string $label = null) : BaseCommand{
        $label = trim(strtolower($label ?? $this->getName()));
        return new BaseCommand($label, $this, $this->getCommandConfig()->getData($label));
    }
}
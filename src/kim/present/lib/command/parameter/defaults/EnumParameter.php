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

namespace kim\present\lib\command\parameter\defaults;

use kim\present\lib\command\overload\Overload;
use kim\present\lib\command\parameter\Parameter;
use kim\present\lib\stringutils\StringUtils as Str;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

abstract class EnumParameter extends Parameter{
    /** @var bool Whether it should be written in exactly full name */
    protected $exact = true;

    /** @var bool Whether to check case */
    protected $caseSensitive = false;

    public function getType() : int{
        return AvailableCommandsPacket::ARG_TYPE_STRING;
    }

    public function getTypeName() : string{
        return "unknown";
    }

    public function toUsageString(Overload $overload, ?CommandSender $sender = null) : string{
        if($this->enum === null)
            return parent::toUsageString($overload, $sender);

        if(count($this->enum->getValues()) === 1)
            return $this->enum->getValues()[0];

        $name = $this->getTranslatedName($overload, $sender) . ": " . $this->enum->getName();
        return $this->isOptional() ? "[$name]" : "<$name>";
    }

    /** @return mixed|null */
    public function parseSilent(CommandSender $sender, string $argument){
        if($this->enum !== null){
            if($this->isExact()){
                foreach($this->enum->getAll() as $name => $value){
                    if(Str::equals($argument, (string) $name, $this->isCaseSensitive())){
                        return $value;
                    }
                }
                return null;
            }

            $found = null;
            $length = strlen($argument);
            $minDiff = PHP_INT_MAX;
            foreach($this->enum->getAll() as $name => $value){
                if(Str::startsWith($argument, (string) $name, $this->isCaseSensitive())){
                    $diff = strlen($name) - $length;
                    if($diff < $minDiff){
                        $found = $value;
                        if($diff === 0)
                            break;

                        $minDiff = $diff;
                    }
                }
            }
            return $found;
        }
        return null;
    }

    public function isExact() : bool{
        return $this->exact;
    }

    public function setExact(bool $exact) : EnumParameter{
        $this->exact = $exact;
        return $this;
    }

    public function isCaseSensitive() : bool{
        return $this->caseSensitive;
    }

    public function setCaseSensitive(bool $caseSensitive) : EnumParameter{
        $this->caseSensitive = $caseSensitive;
        return $this;
    }
}
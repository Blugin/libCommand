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

namespace blugin\lib\command\parameter\defaults;

use blugin\lib\command\parameter\Parameter;
use pocketmine\command\CommandSender;

abstract class EnumParameter extends Parameter{
    /** @var bool Whether it should be written in exactly full name */
    protected $exact = true;

    /** @var bool Whether to check case */
    protected $caseSensitive = false;

    public function getType() : int{
        return -1;
    }

    /**
     * @return string|null
     */
    public function parseSilent(CommandSender $sender, string $argument){
        if($this->enum !== null){
            if($this->isExact()){
                foreach($this->enum->getValues() as $value){
                    if(($this->isCaseSensitive() ? strcmp($argument, $value) : strcasecmp($argument, $value)) === 0){
                        return $value;
                    }
                }
                return null;
            }

            $found = null;
            $length = strlen($argument);
            $minDiff = PHP_INT_MAX;
            foreach($this->enum->getValues() as $value){
                if(($this->isCaseSensitive() ? strpos($argument, $value) : stripos($argument, $value)) === 0){
                    $diff = strlen($value) - $length;
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
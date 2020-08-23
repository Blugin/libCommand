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

namespace blugin\lib\command\parameter;

use blugin\lib\command\BaseCommand;
use blugin\lib\command\ParameterLine;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

abstract class Parameter extends CommandParameter{
    /** @var ParameterLine|null */
    protected $owningLine = null;

    /** @var int length of parameter */
    protected $length = 1;

    public function __construct(?ParameterLine $owningLine, string $name = null, bool $optional = false, ?CommandEnum $enum = null){
        $this->owningLine = $owningLine;

        $this->paramName = $name;
        $this->isOptional = $optional;
        $this->enum = $enum;
        $this->paramType = $this->getParamType();
    }

    public function getOwningLine() : ?ParameterLine{
        return $this->owningLine;
    }

    public function setOwningLine(ParameterLine $owningLine) : Parameter{
        $this->owningLine = $owningLine;
        return $this;
    }

    public function getBaseCommand() : ?BaseCommand{
        return $this->owningLine !== null ? $this->owningLine->getBaseCommand() : null;
    }

    /** @param string[] $params */
    public function sendMessage(CommandSender $sender, string $str, array $params = []) : void{
        $this->getBaseCommand()->sendMessage($sender, $str, $params);
    }

    public function getName() : ?string{
        return $this->paramName;
    }

    public function setName(?string $name) : Parameter{
        $this->paramName = $name;
        return $this;
    }

    public function getParamType() : int{
        return $this->getType() | AvailableCommandsPacket::ARG_FLAG_VALID;
    }

    public function isOptional() : bool{
        return $this->isOptional;
    }

    public function setOptional(bool $isOptional) : Parameter{
        $this->isOptional = $isOptional;
        return $this;
    }

    public function getFlags() : int{
        return $this->flags;
    }

    public function getEnum() : ?CommandEnum{
        return $this->enum;
    }

    public function setEnum(?CommandEnum $enum) : Parameter{
        $this->enum = $enum;
        return $this;
    }

    public function getPostfix() : ?string{
        return $this->postfix;
    }

    public function setPostfix(?string $postfix) : Parameter{
        $this->postfix = $postfix;
        return $this;
    }

    public function getLength() : int{
        return $this->length;
    }

    public function setLength(int $length) : Parameter{
        $this->length = $length;
        return $this;
    }

    public function getFailureMessage(CommandSender $sender, string $argument) : ?string{
        return "commands.generic.parameter.invalid";
    }

    public function toUsageString() : string{
        $name = $this->getName() . ": " . $this->getTypeName();
        return $this->isOptional() ? "[$name]" : "<$name>";
    }

    public function prepare() : Parameter{
        return $this;
    }

    public function valid(CommandSender $sender, string $argument) : bool{
        return $this->parseSilent($sender, $argument) !== null;
    }

    /** @return string */
    public function parse(CommandSender $sender, string $argument){
        $result = $this->parseSilent($sender, $argument);
        if($result !== null)
            return $result;

        $failureMessage = $this->getFailureMessage($sender, $argument);
        if(is_string($failureMessage)){
            $this->getBaseCommand()->sendMessage($sender, $failureMessage, explode(" ", $argument));
        }
        return null;
    }

    /** @return string */
    public function parseSilent(CommandSender $sender, string $argument){
        return $argument;
    }

    abstract public function getType() : int;

    abstract public function getTypeName() : string;
}
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

namespace blugin\lib\command;

use blugin\lib\command\parameter\Parameter;
use pocketmine\command\CommandSender;

class ParameterLine{
    public const ERROR_NAME_MISMATCH = -1;
    public const ERROR_PARAMETER_INSUFFICIENT = -2;
    public const ERROR_PARAMETER_INVALID = -3;

    /** @var BaseCommand */
    protected $baseCommand;

    /** @var string|null */
    protected $name;

    /** @var Parameter[] */
    protected $parameters = [];

    /** @var int */
    protected $requireLength = 0;

    public function __construct(BaseCommand $baseCommand, ?string $name = null){
        $this->baseCommand = $baseCommand;
        $this->name = $name;
    }

    public function getBaseCommand() : BaseCommand{
        return $this->baseCommand;
    }

    public function getName() : ?string{
        return $this->name;
    }

    public function setName(?string $name) : ParameterLine{
        $this->name = $name;
        return $this;
    }

    /** @return Parameter[] */
    public function getParameters() : array{
        return $this->parameters;
    }

    public function addParamater(Parameter $parameter) : ParameterLine{
        $index = count($this->parameters);
        $before = $this->parameters[$index - 1] ?? null;
        if($before instanceof Parameter){
            if($before->getLength() === PHP_INT_MAX)
                throw new \RuntimeException("You can't register parameter after infinite-length parameter");

            if($before->isOptional() && !$parameter->isOptional())
                throw new \RuntimeException("You can't register not-optional parameter after optional parameter");

            foreach($this->parameters as $oldParam){
                if($oldParam->getName() === $parameter->getName())
                    throw new \RuntimeException("You can't register multiple parameters with the same name");
            }
        }

        $this->parameters[] = $parameter;
        if(!$parameter->isOptional()){
            $this->requireLength += $parameter->getLength();
        }
        return $this;
    }

    public function getRequireLength() : int{
        return $this->requireLength;
    }

    /** @param string[] $args */
    public function valid(CommandSender $sender, array $args) : bool{
        if($this->name !== null){
            if(strcasecmp($this->name, array_pop($args)) !== 0)
                return false;
        }

        $requireCount = $this->getRequireLength();
        $argsCount = count($args);
        if($argsCount < $requireCount)
            return false;

        $offset = 0;
        foreach($this->parameters as $parameter){
            if($offset > $argsCount)
                break;

            $argument = implode(" ", array_slice($args, $offset, $parameter->getLength()));
            if($parameter->valid($sender, $argument))
                return false;

            $offset += $parameter->getLength();
        }
        return true;
    }

    /**
     * @param string[] $args
     *
     * @return mixed[]|int name => value. if parse failed return int
     */
    public function parse(CommandSender $sender, array $args){
        if($this->name !== null){
            if(strcasecmp($this->name, array_pop($args)) !== 0)
                return self::ERROR_NAME_MISMATCH;
        }

        $requireCount = $this->getRequireLength();
        $argsCount = count($args);
        if($argsCount < $requireCount)
            return self::ERROR_PARAMETER_INSUFFICIENT;

        $offset = 0;
        $results = [];
        foreach($this->parameters as $parameter){
            if($offset > $argsCount)
                break;

            $argument = implode(" ", array_slice($args, $offset, $parameter->getLength()));
            $result = $parameter->parse($sender, $argument);
            if($result === null)
                return self::ERROR_PARAMETER_INVALID;

            $offset += $parameter->getLength();
            $results[$parameter->getName()] = $result;
        }
        return $results;
    }
}
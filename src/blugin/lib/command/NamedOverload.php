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

use blugin\lib\command\parameter\additions\ConstParameter;
use blugin\lib\command\parameter\Parameter;
use pocketmine\command\CommandSender;

class NamedOverload extends Overload{
    /** @var string */
    protected $label;

    /** @var string */
    protected $name;

    /** @var string[] */
    private $aliases = [];

    public function __construct(BaseCommand $baseCommand, string $name = null){
        $this->baseCommand = $baseCommand;
        $this->setLabel($name);
        $this->setName($name);
    }

    public function getFullMessage(string $str) : string{
        return "commands.{$this->baseCommand->getLabel()}.{$this->getLabel()}$str";
    }

    public function getName() : ?string{
        return $this->name;
    }

    public function setName(?string $name) : Overload{
        $this->name = $name;
        return $this;
    }

    public function getLabel() : string{
        return $this->label;
    }

    public function setLabel(string $label) : Overload{
        $this->label = strtolower($label);
        return $this;
    }

    /** @return string[] */
    public function getAliases() : array{
        return $this->aliases;
    }

    /** @param string[] $aliases */
    public function setAliases(array $aliases) : Overload{
        $this->aliases = $aliases;
        return $this;
    }

    public function getPermission() : string{
        return $this->baseCommand->getPermission() . "." . $this->name;
    }

    /** @return Parameter[] */
    public function getParameters() : array{
        return array_merge([$this->getNameParameter()], parent::getParameters());
    }

    public function toUsageString() : string{
        return $this->name . " " . parent::toUsageString();
    }

    public function getNameParameter(?bool $exact = false, ?string $name = null) : ConstParameter{
        return (new ConstParameter($name ?? $this->name))->setOverload($this)->setExact($exact);
    }

    public function testName(CommandSender $sender, ?string $name) : bool{
        if($name === null)
            return false;

        if($this->getNameParameter()->parseSilent($sender, $name) !== null)
            return true;

        foreach($this->aliases as $alias){
            if($this->getNameParameter(false, $alias)->parseSilent($sender, $name) !== null)
                return true;
        }
        return false;
    }

    /** @param string[] $args */
    public function valid(CommandSender $sender, array $args) : bool{
        return $this->testName($sender, array_shift($args));
    }

    /**
     * @param string[] $args
     *
     * @return mixed[]|int name => value. if parse failed return int
     */
    public function parse(CommandSender $sender, array $args){
        if(!$this->testName($sender, array_shift($args)))
            return self::ERROR_NAME_MISMATCH;

        return parent::parse($sender, $args);
    }
}
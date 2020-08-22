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

namespace blugin\lib\command\exception;

use blugin\lib\command\exception\defaults\GenericInvalidBlockException;
use blugin\lib\command\exception\defaults\GenericInvalidItemException;
use blugin\lib\command\exception\defaults\GenericInvalidNumberException;
use blugin\lib\command\exception\defaults\GenericInvalidPlayerException;
use blugin\lib\command\exception\defaults\GenericInvalidWorldException;
use blugin\lib\command\exception\defaults\GenericNumberTooBigException;
use blugin\lib\command\exception\defaults\GenericNumberTooSmallException;
use blugin\lib\command\exception\defaults\ArgumentLackException;
use blugin\lib\command\BaseCommand;
use blugin\lib\command\Subcommand;
use DaveRandom\CallbackValidator\BuiltInTypes;
use DaveRandom\CallbackValidator\CallbackType;
use DaveRandom\CallbackValidator\ParameterType;
use DaveRandom\CallbackValidator\ReturnType;
use pocketmine\command\CommandSender;
use pocketmine\utils\Utils;

class ExceptionHandler{
    /** @var BaseCommand */
    private $mainCommand;

    /** @var \Closure[] exception class => handler */
    private $handlers = [];

    public function __construct(BaseCommand $mainCommand){
        $this->mainCommand = $mainCommand;

        //Register default handlers
        $this->register(ArgumentLackException::class);
        $this->register(GenericInvalidBlockException::class);
        $this->register(GenericInvalidItemException::class);
        $this->register(GenericInvalidNumberException::class);
        $this->register(GenericInvalidPlayerException::class);
        $this->register(GenericInvalidWorldException::class);
        $this->register(GenericNumberTooBigException::class);
        $this->register(GenericNumberTooSmallException::class);
    }

    public function handle(\Exception $exception, CommandSender $sender, Subcommand $subcommand) : bool{
        $className = get_class($exception);
        if(!isset($this->handlers[$className]))
            return false;

        $this->handlers[$className]($exception, $sender, $subcommand, $this->mainCommand);
        return true;
    }

    /**
     * @param null|\Closure $handlerFunc \Closure(\Exception $e, CommandSender $sender, Subcommand $subcommand, MainCommand $command)
     */
    public function register(string $className, ?\Closure $handlerFunc = null) : void{
        Utils::testValidInstance($className, \Exception::class);
        if($handlerFunc === null){
            $instance = new $className();
            if($instance instanceof IHandleable){
                $handlerFunc = $instance::getHandler();
            }else{
                throw new \TypeError("$className is Not instanceof IHandleable. Must require '\\Closure \$handerFunc' parameter.");
            }
        }
        $sig = new CallbackType(
            new ReturnType(BuiltInTypes::VOID),
            new ParameterType("e", \Exception::class, ParameterType::COVARIANT),
            new ParameterType("sender", CommandSender::class),
            new ParameterType("subcommand", Subcommand::class,ParameterType::COVARIANT | ParameterType::OPTIONAL),
            new ParameterType("command", BaseCommand::class,  ParameterType::COVARIANT | ParameterType::OPTIONAL)
        );
        if(!$sig->isSatisfiedBy($handlerFunc)){
            throw new \TypeError("Declaration of callable `" . CallbackType::createFromCallable($handlerFunc) . "` must be compatible with `" . $sig . "`");
        }

        $this->handlers[$className] = $handlerFunc;
    }
}
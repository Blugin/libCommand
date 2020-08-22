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
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  Blugin team
 * @link    https://github.com/Blugin
 * @license https://www.gnu.org/licenses/lgpl-3.0 LGPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace blugin\lib\command\validator\defaults;

use blugin\lib\command\exception\defaults\GenericInvalidNumberException;
use blugin\lib\command\exception\defaults\GenericNumberTooBigException;
use blugin\lib\command\exception\defaults\GenericNumberTooSmallException;
use blugin\lib\command\validator\ArgumentValidator;

class NumberArgumentValidator implements ArgumentValidator{
    public static function validate(string $argument) : float{
        if(!is_numeric($argument))
            throw new GenericInvalidNumberException($argument);

        return (float) $argument;
    }

    public static function validateMin(string $argument, float $min) : float{
        $num = self::validate($argument);
        if($min !== null && $num < $min)
            throw new GenericNumberTooSmallException("$num", "$min");
        return $num;
    }

    public static function validateMax(string $argument, float $max) : float{
        $num = self::validate($argument);
        if($max !== null && $num > $max)
            throw new GenericNumberTooBigException("$num", "$max");
        return $num;
    }

    public static function validateRange(string $argument, float $min, float $max) : float{
        self::validateMin($argument, $min);
        return self::validateMax($argument, $max);
    }
}
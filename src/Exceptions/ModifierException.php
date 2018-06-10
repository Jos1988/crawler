<?php

namespace App\Exceptions;

use Symfony\Component\Config\Definition\Exception\Exception;

class ModifierException extends Exception
{
    /**
     * @param $modifierClassName
     *
     * @return self
     */
    static function InvalidModifierInput($modifierClassName): self
    {
        return new self(sprintf("Invalid in put for %s.", $modifierClassName));
    }
}
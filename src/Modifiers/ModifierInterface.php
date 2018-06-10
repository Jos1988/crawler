<?php

namespace App\Modifiers;

interface ModifierInterface
{
    /**
     * @param $input
     *
     * @return mixed
     */
    public function modify($input);

    /**
     * @param $input
     *
     * @return bool
     */
    public function validateInput($input): bool;
}
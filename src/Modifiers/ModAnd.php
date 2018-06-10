<?php

namespace App\Modifiers;

use App\Exceptions\ModifierException;

class ModAnd implements ModifierInterface
{
    /**
     * @var ModifierInterface[]
     */
    private $modifiers = [];

    /**
     * ModAnd constructor.
     *
     * @param ModifierInterface ...$modifiers
     */
    public function __construct(ModifierInterface ...$modifiers)
    {
        $this->modifiers = $modifiers;
    }

    public function modify($input)
    {
        foreach ($this->modifiers as $modifier) {
            if (false === $modifier->validateInput($input)) {
                throw ModifierException::InvalidModifierInput(get_class($modifier));
            }

            $input = $modifier->modify($input);
        }

        return $input;
    }

    /**
     * @param ModifierInterface $modifier
     *
     * @return ModAnd
     */
    public function addModifier(ModifierInterface $modifier): self
    {
        $this->modifiers[] = $modifier;

        return $this;
    }

    /**
     * @return ModAnd
     */
    public function clearModifiers():self
    {
        $this->modifiers = [];

        return $this;
    }

    /**
     * @param $input
     *
     * @return bool
     */
    public function validateInput($input): bool
    {
        return false === empty($this->modifiers);
    }

}
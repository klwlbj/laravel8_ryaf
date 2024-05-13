<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EnumValueRule implements Rule
{
    protected $enumValues;

    public function __construct(array $enumValues)
    {
        $this->enumValues = $enumValues;
    }

    public function passes($attribute, $value)
    {
        return in_array($value, $this->enumValues);
    }

    public function message()
    {
        return 'The :attribute must be one of: ' . implode(', ', $this->enumValues);
    }
}

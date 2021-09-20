<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AcademicSessionValidation implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
         $values = explode('-', $value);
         if(count($values) != 2) return false;
         return is_int((int)$values[0] ?? false) && is_int((int)$values[1] ?? false);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute value is not correct format.';
    }
}

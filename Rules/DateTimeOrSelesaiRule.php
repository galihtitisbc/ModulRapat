<?php
namespace Modules\Rapat\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class DateTimeOrSelesaiRule implements Rule
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
        if (strtolower($value) === 'selesai') {
            return true;
        }
        try {
            Carbon::parse($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Waktu selesai harus berupa tanggal/waktu yang valid';
    }
}

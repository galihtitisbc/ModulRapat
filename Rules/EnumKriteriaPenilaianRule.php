<?php

namespace Modules\Rapat\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Rapat\Http\Helper\KriteriaPenilaian;

class EnumKriteriaPenilaianRule implements Rule
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
        return in_array($value, [
            KriteriaPenilaian::MELEBIHI_EKSPETASI->value,
            KriteriaPenilaian::SESUAI_EKSPETASI->value,
            KriteriaPenilaian::TIDAK_SESUAI_EKSPETASI->value,
        ]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Kriteria penilaian tidak valid.';
    }
}

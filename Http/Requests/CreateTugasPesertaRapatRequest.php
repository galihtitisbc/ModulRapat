<?php

namespace Modules\Rapat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTugasPesertaRapatRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'deskripsi' => ['required', 'string', 'min:5'],
            'batas_waktu' => ['required', 'date', 'after_or_equal:today'],
        ];
    }
    public function messages()
    {
        return [
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'deskripsi.string' => 'Deskripsi harus berupa teks.',
            'deskripsi.min' => 'Deskripsi minimal terdiri dari :min karakter.',

            'batas_waktu.required' => 'Batas waktu wajib diisi.',
            'batas_waktu.date' => 'Batas waktu harus berupa tanggal yang valid.',
            'batas_waktu.after_or_equal' => 'Batas waktu tidak boleh sebelum hari ini.',
        ];
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}

<?php

namespace Modules\Rapat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadTugasTindakLanjutRapatRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tugas' => 'nullable|required_without_all:kendala,file_tugas',
            'kendala' => 'nullable|required_without_all:tugas,file_tugas',
            'file_tugas' => 'nullable|array|required_without_all:tugas,kendala',
            'file_tugas.*' => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,xls,xlsx,pdf,txt|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'tugas.required_without_all' => 'Isi minimal salah satu: tugas, kendala, atau unggah file.',
            'kendala.required_without_all' => 'Isi minimal salah satu: tugas, kendala, atau unggah file.',
            'file_tugas.required_without_all' => 'Unggah minimal satu file atau isi tugas/kendala.',
            'file_tugas.*.mimes' => 'File harus berformat: jpg, jpeg, png, doc, docx, xls, xlsx, pdf, atau txt.',
            'file_tugas.*.max' => 'Ukuran file maksimal 2MB.',
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

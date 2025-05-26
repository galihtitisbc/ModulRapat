<?php
namespace Modules\Rapat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KepanitiaanRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nama_kepanitiaan'  => 'required|string|max:255',
            'deskripsi'         => 'required|string',
            'tanggal_mulai'     => 'required|date',
            'tanggal_berakhir'  => 'required|date|after_or_equal:tanggal_mulai',
            'tujuan'            => 'required|string|max:255',
            'pimpinan_username' => 'required|exists:pegawais,username',
            'peserta_panitia'   => 'required|array',
            'peserta_panitia.*' => 'exists:pegawais,username',
            'pengarah'          => 'nullable',
            'penanggung_jawab'  => 'nullable',
            'sekretaris'        => 'nullable',
            'koordinator'       => 'nullable',
        ];
    }
    public function messages()
    {
        return [
            'nama_kepanitiaan.required'       => 'Nama kepanitiaan wajib diisi.',
            'nama_kepanitiaan.string'         => 'Nama kepanitiaan harus berupa teks.',
            'nama_kepanitiaan.max'            => 'Nama kepanitiaan tidak boleh lebih dari :max karakter.',

            'deskripsi.required'              => 'Deskripsi wajib diisi.',
            'deskripsi.string'                => 'Deskripsi harus berupa teks.',

            'tanggal_mulai.required'          => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date'              => 'Tanggal mulai harus berupa tanggal yang valid.',

            'tanggal_berakhir.required'       => 'Tanggal berakhir wajib diisi.',
            'tanggal_berakhir.date'           => 'Tanggal berakhir harus berupa tanggal yang valid.',
            'tanggal_berakhir.after_or_equal' => 'Tanggal berakhir harus sama dengan atau setelah tanggal mulai.',

            'tujuan.required'                 => 'Tujuan wajib diisi.',
            'tujuan.string'                   => 'Tujuan harus berupa teks.',
            'tujuan.max'                      => 'Tujuan tidak boleh lebih dari :max karakter.',

            'pimpinan_username.required'      => 'Pimpinan rapat wajib dipilih.',
            'pimpinan_username.exists'        => 'Pimpinan yang dipilih tidak ditemukan di database.',

            'peserta_panitia.required'        => 'Peserta panitia wajib diisi.',
            'peserta_panitia.array'           => 'Peserta panitia harus berupa array.',
            'peserta_panitia.*.exists'        => 'Salah satu peserta panitia tidak ditemukan di database.',

            'pengarah.nullable'               => 'Kolom pengarah boleh dikosongkan.',
            'penanggung_jawab.nullable'       => 'Kolom penanggung jawab boleh dikosongkan.',
            'sekretaris.nullable'             => 'Kolom sekretaris boleh dikosongkan.',
            'koordinator.nullable'            => 'Kolom koordinator boleh dikosongkan.',
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

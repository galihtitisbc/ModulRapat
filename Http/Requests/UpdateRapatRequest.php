<?php
namespace Modules\Rapat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRapatRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'pimpinan_username' => 'required|exists:pegawais,username',
            'kepanitiaan_id'    => 'nullable|exists:kepanitiaans,id',
            'peserta_rapat'     => 'required|array',
            'peserta_rapat.*'   => 'exists:pegawais,username',
            'notulis_username'  => 'required|exists:pegawais,username',
            'nomor_surat'       => 'required|string|max:255',
            'waktu_mulai'       => 'required|date_format:Y-m-d H:i:s',
            'waktu_selesai'     => 'required',
            'agenda_rapat'      => 'required|string',
            'tempat'            => 'required|string|max:255',
            'lampiran.*'        => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,xls,xlsx,pdf,txt|max:2048',
        ];
    }
    public function messages()
    {
        return [
            'pimpinan_username.required' => 'Pimpinan rapat wajib dipilih.',
            'pimpinan_username.exists'   => 'Pimpinan yang dipilih tidak ditemukan dalam data pengguna.',

            'kepanitiaan_id.exists'      => 'Kepanitiaan yang dipilih tidak ditemukan.',

            'peserta_rapat.required'     => 'Peserta rapat wajib diisi.',
            'peserta_rapat.array'        => 'Peserta rapat harus berupa array.',
            'peserta_rapat.*.exists'     => 'Salah satu peserta rapat tidak ditemukan dalam data pengguna.',

            'notulis_username.required'  => 'Notulis wajib dipilih.',
            'notulis_username.exists'    => 'Notulis yang dipilih tidak ditemukan dalam data pengguna.',

            'nomor_surat.required'       => 'Nomor surat wajib diisi.',
            'nomor_surat.string'         => 'Nomor surat harus berupa teks.',
            'nomor_surat.max'            => 'Nomor surat tidak boleh lebih dari :max karakter.',

            'waktu_mulai.required'       => 'Waktu mulai rapat wajib diisi.',
            'waktu_mulai.date_format'    => 'Format waktu mulai tidak sesuai. Gunakan format Y-m-d H:i:s.',

            'waktu_selesai.required'     => 'Waktu selesai rapat wajib diisi.',
            'waktu_selesai.date_format'  => 'Format waktu selesai tidak sesuai. Gunakan format Y-m-d H:i:s.',
            'waktu_selesai.after'        => 'Waktu selesai harus setelah waktu mulai.',

            'agenda_rapat.required'      => 'Agenda rapat wajib diisi.',
            'agenda_rapat.string'        => 'Agenda rapat harus berupa teks.',

            'tempat.required'            => 'Tempat rapat wajib diisi.',
            'tempat.string'              => 'Tempat rapat harus berupa teks.',
            'tempat.max'                 => 'Tempat rapat tidak boleh lebih dari :max karakter.',

            'lampiran.*.file'            => 'Setiap lampiran harus berupa file.',
            'lampiran.*.mimes'           => 'Jenis file lampiran tidak didukung. Format yang diperbolehkan: jpg, jpeg, png, doc, docx, xls, xlsx, pdf, txt.',
            'lampiran.*.max'             => 'Ukuran file lampiran maksimal 2MB.',
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

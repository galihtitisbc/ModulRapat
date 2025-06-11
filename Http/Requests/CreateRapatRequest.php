<?php
namespace Modules\Rapat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Rapat\Rules\DateTimeOrSelesaiRule;

class CreateRapatRequest extends FormRequest
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
            'waktu_mulai'       => 'required|after:now',
            'waktu_selesai'     => ['required', new DateTimeOrSelesaiRule],
            'agenda_rapat'      => 'required|string',
            'tempat'            => 'required|string|max:255',
            'lampiran.*'        => 'nullable|file|mimes:jpg,jpeg,png,doc,docx,xls,xlsx,pdf,txt|max:2048',
        ];
    }
    public function messages()
    {
        return [
            'pimpinan_username.required'  => 'Pimpinan rapat harus dipilih.',
            'pimpinan_username.exists'    => 'Pimpinan rapat tidak valid.',

            'kepanitiaan_username.exists' => 'Kepanitiaan yang dipilih tidak valid.',

            'peserta_rapat.required'      => 'Peserta rapat harus diisi.',
            'peserta_rapat.array'         => 'Format peserta rapat tidak sesuai.',
            'peserta_rapat.*.exists'      => 'Peserta rapat tidak valid.',

            'notulis_username.required'   => 'Notulis rapat harus dipilih.',
            'notulis_username.exists'     => 'Notulis rapat tidak valid.',

            'nomor_surat.required'        => 'Nomor surat harus diisi.',
            'nomor_surat.string'          => 'Nomor surat harus berupa teks.',
            'nomor_surat.max'             => 'Nomor surat maksimal 255 karakter.',

            'waktu_mulai.required'        => 'Waktu mulai rapat harus diisi.',
            'waktu_mulai.date_format'     => 'Format waktu mulai tidak sesuai (Y-m-d H:i:s).',

            'waktu_selesai.required'      => 'Waktu selesai rapat harus diisi.',
            'waktu_mulai.after'           => 'Waktu mulai harus setelah waktu saat ini.',
            'agenda_rapat.required'       => 'Agenda rapat harus diisi.',
            'agenda_rapat.string'         => 'Agenda rapat harus berupa teks.',

            'tempat.required'             => 'Tempat rapat harus diisi.',
            'tempat.string'               => 'Tempat rapat harus berupa teks.',
            'tempat.max'                  => 'Tempat rapat maksimal 255 karakter.',

            'lampiran.*.file'             => 'Lampiran harus berupa file.',
            'lampiran.*.mimes'            => 'Lampiran harus berupa file dengan format: jpg, jpeg, png, doc, docx, xls, xlsx, pdf, atau txt.',
            'lampiran.*.max'              => 'Ukuran lampiran maksimal 2MB.',
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

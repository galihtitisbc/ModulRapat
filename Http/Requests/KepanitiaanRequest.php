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
            'nama_kepanitiaan'                  => 'required|string|max:255',
            'deskripsi'                         => 'required|string',
            'tanggal_mulai'                     => 'required|date',
            'tanggal_berakhir'                  => 'required|date|after_or_equal:tanggal_mulai',
            'tujuan'                            => 'required|string|max:255',
            'pimpinan_id'                       => 'required|exists:pegawais,id',
            'peserta_panitia'                   => 'required|array',
            'peserta_panitia.*'                 => 'exists:pegawais,id',
            'struktur_kepanitiaan'              => 'required|array',
            'struktur_kepanitiaan.*.jabatan'    => 'required|string',
            'struktur_kepanitiaan.*.pegawai_id' => 'required|string|exists:pegawais,id',
        ];
    }
    public function messages()
    {
        return [
            'nama_kepanitiaan.required'                  => 'Nama kepanitiaan wajib diisi.',
            'nama_kepanitiaan.string'                    => 'Nama kepanitiaan harus berupa teks.',
            'nama_kepanitiaan.max'                       => 'Nama kepanitiaan tidak boleh lebih dari :max karakter.',

            'deskripsi.required'                         => 'Deskripsi wajib diisi.',
            'deskripsi.string'                           => 'Deskripsi harus berupa teks.',

            'tanggal_mulai.required'                     => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date'                         => 'Tanggal mulai harus berupa tanggal yang valid.',

            'tanggal_berakhir.required'                  => 'Tanggal berakhir wajib diisi.',
            'tanggal_berakhir.date'                      => 'Tanggal berakhir harus berupa tanggal yang valid.',
            'tanggal_berakhir.after_or_equal'            => 'Tanggal berakhir harus sama dengan atau setelah tanggal mulai.',

            'tujuan.required'                            => 'Tujuan wajib diisi.',
            'tujuan.string'                              => 'Tujuan harus berupa teks.',
            'tujuan.max'                                 => 'Tujuan tidak boleh lebih dari :max karakter.',

            'pimpinan_id.required'                       => 'Pimpinan Panitia wajib dipilih.',
            'pimpinan_id.exists'                         => 'Pimpinan yang dipilih tidak ditemukan di database.',

            'peserta_panitia.required'                   => 'Peserta panitia wajib diisi.',
            'peserta_panitia.array'                      => 'Peserta panitia harus berupa array.',
            'peserta_panitia.*.exists'                   => 'Salah satu peserta panitia tidak ditemukan di database.',

            'pengarah.nullable'                          => 'Kolom pengarah boleh dikosongkan.',
            'penanggung_jawab.nullable'                  => 'Kolom penanggung jawab boleh dikosongkan.',
            'sekretaris.nullable'                        => 'Kolom sekretaris boleh dikosongkan.',
            'koordinator.nullable'                       => 'Kolom koordinator boleh dikosongkan.',
            'struktur_kepanitiaan.required'              => 'Data struktur kepanitiaan wajib diisi.',
            'struktur_kepanitiaan.array'                 => 'Format struktur kepanitiaan harus berupa array.',

            'struktur_kepanitiaan.*.jabatan.required'    => 'Jabatan pada setiap entri wajib diisi.',
            'struktur_kepanitiaan.*.jabatan.string'      => 'Jabatan harus berupa teks.',

            'struktur_kepanitiaan.*.pegawai_id.required' => 'pegawai_id pada setiap entri wajib diisi.',
            'struktur_kepanitiaan.*.pegawai_id.string'   => 'pegawai_id harus berupa teks.',
            'struktur_kepanitiaan.*.pegawai_id.exists'   => 'pegawai_id yang dimasukkan tidak terdaftar.',
        ];
    }
    protected function prepareForValidation()
    {
        if ($this->has('struktur_kepanitiaan') && is_string($this->struktur_kepanitiaan)) {
            $this->merge([
                'struktur_kepanitiaan' => json_decode($this->struktur_kepanitiaan, true),
            ]);
        }
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

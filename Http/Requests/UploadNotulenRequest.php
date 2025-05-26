<?php
namespace Modules\Rapat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadNotulenRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'peserta_hadir'      => 'required|array',
            'peserta_hadir.*'    => 'exists:pegawais,username',
            'catatan_rapat'      => 'nullable|string|required_without:notulen_file',
            'notulen_file'       => 'nullable|array|required_without:catatan_rapat',
            'notulen_file.*'     => 'file|mimes:pdf,doc,docx|max:5120',
            'dokumentasi_file'   => 'required|array',
            'dokumentasi_file.*' => 'file|mimes:jpg,jpeg,png,PNG|max:2048',
        ];
    }
    public function messages(): array
    {
        return [
            'peserta_hadir.required'         => 'Daftar peserta hadir wajib diisi.',
            'peserta_hadir.array'            => 'Format peserta hadir tidak valid.',
            'peserta_hadir.*.exists'         => 'Peserta yang dipilih tidak ditemukan dalam sistem.',
            'catatan_rapat.required'         => 'Catatan rapat wajib diisi.',
            'catatan_rapat.string'           => 'Catatan rapat harus berupa teks.',
            'notulen_file.array'             => 'Format file notulen tidak valid.',
            'notulen_file.*.file'            => 'Setiap file notulen harus berupa file.',
            'notulen_file.*.mimes'           => 'File notulen harus berformat PDF, DOC, atau DOCX.',
            'notulen_file.*.max'             => 'Ukuran file notulen maksimal 5MB.',
            'catatan_rapat.required_without' => 'Catatan rapat wajib diisi jika file notulen tidak diunggah.',
            'notulen_file.required_without'  => 'File notulen wajib diunggah jika catatan rapat tidak diisi.',
            'dokumentasi_file.array'         => 'Format file dokumentasi tidak valid.',
            'dokumentasi_file.required'      => 'File Dokumentasi wajib diunggah.',
            'dokumentasi_file.*.image'       => 'Setiap file dokumentasi harus berupa gambar.',
            'dokumentasi_file.*.mimes'       => 'File dokumentasi harus berformat JPG, JPEG, atau PNG.',
            'dokumentasi_file.*.max'         => 'Ukuran file dokumentasi maksimal 5MB.',
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

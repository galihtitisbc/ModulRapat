<?php
namespace Modules\Rapat\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Rapat\Entities\Kepanitiaan;
use Modules\Rapat\Entities\Pegawai;

class RapatKepanitiaanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $panitia = Kepanitiaan::create([
            'pimpinan_id'      => 26,
            'nama_kepanitiaan' => 'Panitia infrastruktur',
            'slug'             => 'panitia-infrastruktur',
            'access_token'     => Str::uuid(),
            'deskripsi'        => 'Panitia yang mengorganisir infrastruktur tahunan',
            'tanggal_mulai'    => now()->subDays(20)->toDateString(),
            'tanggal_berakhir' => now()->addDays(5)->toDateString(),
            'tujuan'           => 'menyediakan infrastruktur.',
            'status'           => 'AKTIF',
            'struktur'         => "[{\"jabatan\":\"Wakil Ketua\",\"pegawai_id\":\"114\"},{\"jabatan\":\"Sekretaris\",\"pegawai_id\":\"140\"},{\"jabatan\":\"Anggota\",\"pegawai_id\":\"187\"},{\"jabatan\":\"Anggota\",\"pegawai_id\":\"196\"}]",
        ]);
        $pegawai = [26, 114, 140, 187, 196];
        $panitia->pegawai()->attach($pegawai);
    }
}

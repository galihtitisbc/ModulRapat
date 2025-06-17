<?php
namespace Modules\Rapat\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
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
            'pimpinan_username' => 'banksonk',
            'nama_kepanitiaan'  => 'Panitia infrastruktur',
            'slug'              => 'panitia-infrastruktur',
            'deskripsi'         => 'Panitia yang mengorganisir infrastruktur tahunan',
            'tanggal_mulai'     => now()->subDays(20)->toDateString(),
            'tanggal_berakhir'  => now()->addDays(5)->toDateString(),
            'tujuan'            => 'menyediakan infrastruktur.',
            'status'            => 'AKTIF',
            'struktur'          => "[{\"jabatan\":\"Wakil Ketua\",\"username\":\"adityawisanjaya\"},{\"jabatan\":\"Sekretaris\",\"username\":\"afikamilda\"},{\"jabatan\":\"Anggota\",\"username\":\"ahanafi\"},{\"jabatan\":\"Anggota\",\"username\":\"agungnursabilillah\"}]",
        ]);
        $pegawai = ['adityawisanjaya', 'afikamilda', 'ahanafi', 'agungnursabilillah'];
        $panitia->pegawai()->attach($pegawai);
    }
}

<?php
namespace Modules\Rapat\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Rapat\Entities\Pegawai;
use Modules\Rapat\Entities\RapatAgenda;
use Modules\Rapat\Http\Helper\StatusPesertaRapat;

class PesertaRapatTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $pegawai = Pegawai::all();
        RapatAgenda::factory(2)->create();
        $status = [StatusPesertaRapat::BERSEDIA->value, StatusPesertaRapat::TIDAK_BERSEDIA->value, StatusPesertaRapat::HADIR->value, StatusPesertaRapat::TIDAK_HADIR->value, StatusPesertaRapat::MENUNGGU->value];
        RapatAgenda::each(function ($rapatAgenda) use ($pegawai, $status) {
            $pivotArray = [];
            $pegawaiIds = $pegawai->random(rand(2, 7))->pluck('username')->toArray();
            foreach ($pegawaiIds as $pegawaiId) {
                $pivotArray[] = ['pegawai_username' => $pegawaiId, 'status' => $status[rand(0, 4)], 'is_penugasan' => rand(0, 1), 'link_konfirmasi' => 'https://google.com'];
            }
            $rapatAgenda->rapatAgendaPeserta()->attach($pivotArray);
        });
    }
}

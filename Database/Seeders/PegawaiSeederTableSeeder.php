<?php
namespace Modules\Rapat\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PegawaiSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $json = File::get(__DIR__ . '/../../Database/data/pegawai.json');
        $data = json_decode($json);
        DB::table('pegawais')->insert(
            [
                'nip'             => '1943345',
                'nama'            => 'PIMPINAN',
                'staff'           => 4,
                'jurusan'         => 4,
                'prodi'           => null,
                'jenis_kelamin'   => 'L',
                'gelar_dpn'       => null,
                'gelar_blk'       => null,
                'status_karyawan' => 3,
                'username'        => 'pimpinan',
                'noid'            => null,
            ]
        );
        DB::table('pegawais')->insert(
            [
                'nip'             => '1943345',
                'nama'            => 'Devit Suwardiyanto',
                'staff'           => 4,
                'jurusan'         => 4,
                'prodi'           => null,
                'jenis_kelamin'   => 'L',
                'gelar_dpn'       => null,
                'gelar_blk'       => null,
                'status_karyawan' => 3,
                'username'        => 'devit',
                'noid'            => null,
            ]
        );
        foreach ($data->data as $item) {
            DB::table('pegawais')->insert([
                'nip'             => $item->nip ?? '-',
                'nama'            => $item->nama ?? '-',
                'staff'           => isset($item->staff) ? (int) $item->staff : null,
                'jurusan'         => isset($item->jurusan) ? (int) $item->jurusan : null,
                'prodi'           => isset($item->prodi) ? (int) $item->prodi : null,
                'jenis_kelamin'   => in_array($item->jenis_kelamin ?? null, ['L', 'P']) ? $item->jenis_kelamin : null,
                'gelar_dpn'       => $item->gelar_dpn ?? null,
                'gelar_blk'       => $item->gelar_blk ?? null,
                'status_karyawan' => $item->status_karyawan ?? null,
                'username'        => $item->username ?? null,
                'noid'            => $item->noid ?? null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }
}

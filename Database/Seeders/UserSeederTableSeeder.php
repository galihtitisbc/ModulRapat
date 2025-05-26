<?php
namespace Modules\Rapat\Database\Seeders;

use App\Models\Core\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $pimpinan = User::create([
            'name'       => 'Pimpinan',
            'username'   => 'pimpinan',
            'email'      => 'p@gmail.com',
            'password'   => Hash::make('password'),
            'role_aktif' => 'direktur',
            'unit'       => 0,
            'staff'      => 0,
            'status'     => 2,
        ]);
        $pimpinan->assignRole(['direktur']);
        $wadir = User::create([
            'name'       => 'Devit Suwardiyanto',
            'username'   => 'devit',
            'email'      => 'devit@gmail.com',
            'password'   => Hash::make('password'),
            'role_aktif' => 'wadir2',
            'unit'       => 0,
            'staff'      => 0,
            'status'     => 2,
        ]);
        $wadir->assignRole(['wadir2', 'dosen']);
        $roles = [
            'kepegawaian',
            'dosen',
            'pegawai',
            'wadir1',
            'wadir3',
            'kaprodi',
            'kajur',
            'p2m',
            'kaunit',
            'kalab',
            'keuangan',
            'sekjur',
        ];

        $pegawaiRecords = DB::table('pegawais')->whereNotIn('id', [1, 2])->get();
        foreach ($pegawaiRecords as $pegawai) {
            $randomRole = $roles[array_rand($roles)];

            $email = $pegawai->username . '@example.com';

            $user = User::create([
                'username'   => $pegawai->username ?? 'Tidak Ada Username',
                'name'       => $randomRole . ' ' . $pegawai->nama,
                'email'      => $email,
                'password'   => Hash::make('password'),
                'role_aktif' => $randomRole,
                'unit'       => $pegawai->jurusan ?? 0,
                'staff'      => $pegawai->staff ?? 0,
                'status'     => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $user->assignRole($randomRole);
        }
    }
}

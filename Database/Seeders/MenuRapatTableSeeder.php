<?php
namespace Modules\Rapat\Database\Seeders;

use App\Models\Core\Menu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class MenuRapatTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        Menu::where('modul', 'Rapat')->delete();
        $menu = Menu::create([
            'modul'     => 'Rapat',
            'label'     => 'Rapat',
            'url'       => '',
            // 'can' => serialize(['pimpinan', 'pejabat', 'sekretaris', 'kepegawaian', 'dosen']),
            'can'       => serialize([
                'kepegawaian',
                'dosen',
                'pegawai',
                'direktur',
                'wadir1',
                'wadir2',
                'wadir3',
                'kaprodi',
                'kajur',
                'p2m',
                'kaunit',
                'kalab',
                'keuangan',
                'sekjur',
            ]),
            'icon'      => 'fas fa-address-book',
            'urut'      => 1,
            'parent_id' => 0,
            'active'    => '',
        ]);
        Menu::create([
            'modul'     => 'Rapat',
            'label'     => 'Dashboard',
            'url'       => 'rapat/dashboard',
            // 'can' => serialize(['pimpinan', 'pejabat', 'sekretaris', 'kepegawaian', 'dosen']),
            'can'       => serialize([
                'kepegawaian',
                'dosen',
                'pegawai',
                'direktur',
                'wadir1',
                'wadir2',
                'wadir3',
                'kaprodi',
                'kajur',
                'p2m',
                'kaunit',
                'kalab',
                'keuangan',
                'sekjur',
            ]),
            'icon'      => 'fas fa-home',
            'urut'      => 1,
            'parent_id' => $menu->id,
            'active'    => serialize(['rapat/dashboard', 'rapat/dashboard*']),
        ]);
        Menu::create([
            'modul'     => 'Rapat',
            'label'     => 'Kepanitiaan',
            'url'       => 'rapat/panitia',
            'can'       => serialize([
                'kepegawaian',
                'dosen',
                'pegawai',
                'direktur',
                'wadir1',
                'wadir2',
                'wadir3',
                'kaprodi',
                'kajur',
                'p2m',
                'kaunit',
                'kalab',
                'keuangan',
                'sekjur',
            ]),
            'icon'      => 'fas fa-fw fa-users',
            'urut'      => 2,
            'parent_id' => $menu->id,
            'active'    => serialize(['rapat/panitia', 'rapat/panitia*']),
        ]);
        Menu::create([
            'modul'     => 'Rapat',
            'label'     => 'Agenda Rapat',
            'url'       => 'rapat/agenda-rapat',
            // 'can' => serialize(['pimpinan', 'pejabat', 'sekretaris', 'kepegawaian', 'dosen']),
            'can'       => serialize([
                'kepegawaian',
                'dosen',
                'pegawai',
                'direktur',
                'wadir1',
                'wadir2',
                'wadir3',
                'kaprodi',
                'kajur',
                'p2m',
                'kaunit',
                'kalab',
                'keuangan',
                'sekjur',
            ]),
            'icon'      => 'fas fa-calendar-alt',
            'urut'      => 3,
            'parent_id' => $menu->id,
            'active'    => serialize(['rapat/agenda-rapat', 'rapat/agenda-rapat*']),
        ]);
        Menu::create([
            'modul'     => 'Rapat',
            'label'     => 'Tindak Lanjut Rapat',
            'url'       => 'rapat/tindak-lanjut-rapat',
            // 'can' => serialize(['pimpinan', 'pejabat', 'sekretaris', 'kepegawaian', 'dosen']),
            'can'       => serialize([
                'kepegawaian',
                'dosen',
                'pegawai',
                'direktur',
                'wadir1',
                'wadir2',
                'wadir3',
                'kaprodi',
                'kajur',
                'p2m',
                'kaunit',
                'kalab',
                'keuangan',
                'sekjur',
            ]),
            'icon'      => 'fas fa-list-alt',
            'urut'      => 4,
            'parent_id' => $menu->id,
            'active'    => serialize(['rapat/tindak-lanjut-rapat', 'rapat/tindak-lanjut-rapat*']),
        ]);
        Menu::create([
            'modul'     => 'Rapat',
            'label'     => 'Riwayat Rapat',
            'url'       => 'rapat/riwayat-rapat',
            // 'can' => serialize(['pimpinan', 'pejabat', 'sekretaris', 'kepegawaian', 'dosen']),
            'can'       => serialize([
                'kepegawaian',
                'dosen',
                'pegawai',
                'direktur',
                'wadir1',
                'wadir2',
                'wadir3',
                'kaprodi',
                'kajur',
                'p2m',
                'kaunit',
                'kalab',
                'keuangan',
                'sekjur',
            ]),
            'icon'      => 'fas fa-history',
            'urut'      => 5,
            'parent_id' => $menu->id,
            'active'    => serialize(['rapat/riwayat-rapat', 'rapat/riwayat-rapat*']),
        ]);
    }
}

<?php
namespace Modules\Rapat\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // Semua permission yang dimiliki semua role
        $globalPermissions = [
            //// permission yang sudah ada ( default )
            'adminlte.darkmode.toggle',
            'home.index',
            'login.show',
            'logout.perform',
            'login.perform',
            'ssoLogin',
            'beralih.peran',
            'users.tukaruser',
            'users.editprofile',
            'users.updateprofile',
            // end permission yang sudah ada
            "rapat.dashboard",
            "rapat.agenda.index",
            "rapat.riwayat.index",
            "rapat.riwayat.generate-pdf",
            "rapat.notulis.download",
            "rapat.agenda.download",
            "rapat.konfirmasi.form",
            "rapat.konfirmasi.submit",
            'rapat.tindak-lanjut.index',
            "rapat.tindak-lanjut.detail.tugas",
            "rapat.tindak-lanjut.tugas.unggah.form",
            "rapat.tindak-lanjut.tugas.unggah.submit",
            "rapat.tindak-lanjut.simpan-tugas",
            "rapat.panitia.index",
            "rapat.panitia.detail",
            "rapat.panitia.download",
        ];

// Permission untuk role kepegawaian
        $kepegawaianPermissions = [
            "rapat.panitia.create",
            "rapat.panitia.store",
            "rapat.panitia.edit",
            "rapat.panitia.update",
            "rapat.panitia.change-status",
        ];
// Permission untuk pimpinan rapat
        $pimpinanRapatPermissions = [
            "rapat.agenda.create",
            "rapat.agenda.store",
            "rapat.agenda.edit",
            "rapat.agenda.ajax.edit",
            "rapat.agenda.update",
            "rapat.agenda.batal",
            "rapat.agenda.detail",
            'rapat.agenda.ajax.peserta',
            'rapat.agenda.ajax.selected.peserta',
            'rapat.agenda.ajax.kepanitiaan',
        ];

// Permission notulis
        $notulisPermissions = [
            "rapat.notulis.unggah.form",
            "rapat.notulis.unggah.submit",
        ];

// Role Groups
        $kepegawaianRoles   = ['kepegawaian'];
        $pimpinanRoles      = ['direktur', 'wadir1', 'wadir2', 'wadir3'];
        $pimpinanRapatRoles = ['direktur', 'wadir1', 'wadir2', 'wadir3', 'sekjur', 'kaprodi', 'kajur'];
        $pesertaRoles       = [
            'dosen',
            'pegawai',
            'p2m',
            'kaunit',
            'kalab',
            'keuangan',
        ];

// Assign peserta
        foreach ($pesertaRoles as $roleName) {
            $role = Role::findOrCreate($roleName);
            $role->syncPermissions(array_merge($globalPermissions));
        }
// Assign kepegawaian
        foreach ($kepegawaianRoles as $roleName) {
            $role = Role::findOrCreate($roleName);
            $role->syncPermissions(array_merge($kepegawaianPermissions, $globalPermissions));
        }

// Assign pimpinan
        foreach ($pimpinanRoles as $roleName) {
            $role = Role::findOrCreate($roleName);
            $role->syncPermissions(array_merge($globalPermissions));
        }

// Assign pimpinan rapat
        foreach ($pimpinanRapatRoles as $roleName) {
            $role = Role::findOrCreate($roleName);
            $role->syncPermissions(array_merge($pimpinanRapatPermissions, $globalPermissions));
        }

// Tambahkan permission notulis ke semua role
        $allRoles = array_unique(array_merge($kepegawaianRoles, $pimpinanRoles, $pimpinanRapatRoles));
        foreach ($allRoles as $roleName) {
            $role = Role::findByName($roleName);
            $role->givePermissionTo($notulisPermissions);
        }
    }
}

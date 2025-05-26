<?php

namespace Modules\Rapat\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RapatDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->call([
            MenuRapatTableSeeder::class,
            RoleSeederTableSeeder::class,
            PegawaiSeederTableSeeder::class,
            UserSeederTableSeeder::class,
            PesertaRapatTableSeeder::class,
            RapatKepanitiaanTableSeeder::class
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

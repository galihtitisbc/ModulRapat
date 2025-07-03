<?php
namespace Modules\Rapat\Database\factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Rapat\Entities\Pegawai;

class RapatAgendaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Rapat\Entities\RapatAgenda::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'pegawai_id'    => Pegawai::inRandomOrder()->whereNotNull('username')->first()->id,
            'pimpinan_id'   => Pegawai::inRandomOrder()->whereNotNull('username')->first()->id,
            'notulis_id'    => Pegawai::inRandomOrder()->whereNotNull('username')->first()->id,
            'nomor_surat'   => $this->faker->word,
            'slug'          => $this->faker->slug,
            'waktu_mulai'   => Carbon::now('Asia/Jakarta'),
            'waktu_selesai' => Carbon::now('Asia/Jakarta')->addHours(2),
            'agenda_rapat'  => $this->faker->text,
            'tempat'        => $this->faker->address,
            'status'        => $this->faker->randomElement(['CANCELED', 'SCHEDULED', 'COMPLETED', 'STARTED']),
            'lampiran'      => $this->faker->word,
            'zoom_link'     => $this->faker->url,
            'calendar_link' => $this->faker->url,
        ];
    }
}

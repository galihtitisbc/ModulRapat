<?php
namespace Modules\Rapat\Tests\Unit;

use App\Models\Core\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Modules\Rapat\Http\Service\Implementation\RapatService;
use Tests\TestCase;

class RapatServiceTest extends TestCase
{
    protected $mockService;
    public function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::find(2));
        Bus::fake();
        Queue::fake();
        Storage::fake('rapat');
        $this->mockService = Mockery::mock(RapatService::class);
        $this->app->instance(RapatService::class, $this->mockService);
    }
    public function test_validates_required_fields_store_rapat()
    {
        $response = $this->postJson('/rapat/agenda-rapat/store', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'pimpinan_username',
            'notulis_username',
            'peserta_rapat',
            'nomor_surat',
            'waktu_mulai',
            'waktu_selesai',
            'agenda_rapat',
            'tempat',
        ]);

    }
    public function test_validates_invalid_fields_store_rapat()
    {
        $data = [
            'pimpinan_username' => 'tidak_ada',
            'notulis_username'  => 'juga_tidak_ada',
            'peserta_rapat'     => ['juga_tidak_valid'],
            'kepanitiaan_id'    => 999,
            'nomor_surat'       => str_repeat('A', 256),
            'lampiran'          => [UploadedFile::fake()->create('malware.exe', 100)],
        ];
        $response = $this->postJson('/rapat/agenda-rapat/store', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'pimpinan_username',
            'notulis_username',
            'peserta_rapat.0',
            'kepanitiaan_id',
            'nomor_surat',
            'lampiran.0',
        ]);

    }
    public function test_valid_fields_store_agenda_rapat()
    {
        $this->mockService->shouldReceive('store')->once();
        $data = [
            'pimpinan_username' => 'pimpinan',
            'peserta_rapat'     => ['pimpinan'],
            'notulis_username'  => ['tefa'],
            'nomor_surat'       => '123/RAPAT/2025',
            'waktu_mulai'       => now()->addHours(3)->toDateTimeString(),
            'waktu_selesai'     => now()->addHour()->toDateTimeString(),
            'agenda_rapat'      => 'Agenda rapat penting',
            'tempat'            => 'Ruang Sidang 1',
            'lampiran'          => [UploadedFile::fake()->create('dokumentasi.pdf', 200, 'application/pdf')],
        ];
        $response = $this->postJson('/rapat/agenda-rapat/store', $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true,
            'title'                          => 'Berhasil',
            'message'                        => 'success',
            'message'                        => 'Rapat berhasil ditambahkan.',
            'icon'                           => 'success',
        ]);
    }
    public function test_judul_agenda_tidak_diisi()
    {
        $data = [
            'pimpinan_username' => 'pimpinan',
            'peserta_rapat'     => ['pimpinan'],
            'notulis_username'  => ['tefa'],
            'nomor_surat'       => '123/RAPAT/2025',
            'waktu_mulai'       => now()->addHours(3)->toDateTimeString(),
            'waktu_selesai'     => now()->addHour()->toDateTimeString(),
            'tempat'            => 'Ruang Sidang 1',
            'lampiran'          => [UploadedFile::fake()->create('dokumentasi.pdf', 200, 'application/pdf')],
        ];
        $response = $this->postJson('/rapat/agenda-rapat/store', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['agenda_rapat']);
    }
    public function test_waktu_yang_dimasukkan_sudah_berlalu()
    {
        $data = [
            'pimpinan_username' => 'pimpinan',
            'peserta_rapat'     => ['pimpinan'],
            'agenda_rapat'      => 'Agenda Lama',
            'notulis_username'  => ['tefa'],
            'nomor_surat'       => '123/RAPAT/2025',
            'waktu_mulai'       => now()->subDay()->toDateTimeString(),
            'waktu_selesai'     => 'SELESAI',
            'tempat'            => 'Ruang Sidang 1',
            'lampiran'          => [UploadedFile::fake()->create('dokumentasi.pdf', 200, 'application/pdf')],
        ];
        $response = $this->postJson('/rapat/agenda-rapat/store', $data);
        $response->assertStatus(422);
    }
    public function test_tidak_memilih_ruangan_rapat()
    {
        $response = $this->postJson('/rapat/agenda-rapat/store', [
            'pimpinan_username' => 'pimpinan',
            'peserta_rapat'     => ['pimpinan'],
            'agenda_rapat'      => 'Agenda Lama',
            'notulis_username'  => ['tefa'],
            'nomor_surat'       => 'R-004',
            'waktu_mulai'       => now()->addDay()->format('Y-m-d H:i:s'),
            'waktu_selesai'     => now()->addDays(1)->addHour()->format('Y-m-d H:i:s'),
            'agenda_rapat'      => 'Agenda tanpa tempat',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['tempat']);
    }
    public function test_tidak_memilih_peserta_rapat()
    {
        $response = $this->postJson('/rapat/agenda-rapat/store', [
            'pimpinan_username' => 'pimpinan',
            'agenda_rapat'      => 'Agenda Lama',
            'notulis_username'  => ['tefa'],
            'nomor_surat'       => 'R-004',
            'waktu_mulai'       => now()->addDay()->format('Y-m-d H:i:s'),
            'waktu_selesai'     => now()->addDays(1)->addHour()->format('Y-m-d H:i:s'),
            'agenda_rapat'      => 'Agenda tanpa tempat',
            'tempat'            => 'Aula',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['peserta_rapat']);
    }
    public function test_tidak_memilih_notulis()
    {
        $response = $this->postJson('/rapat/agenda-rapat/store', [
            'pimpinan_username' => 'pimpinan',
            'peserta_rapat'     => ['pimpinan'],
            'agenda_rapat'      => 'Agenda Lama',
            'nomor_surat'       => 'R-004',
            'waktu_mulai'       => now()->addDay()->format('Y-m-d H:i:s'),
            'waktu_selesai'     => now()->addDays(1)->addHour()->format('Y-m-d H:i:s'),
            'agenda_rapat'      => 'Agenda tanpa tempat',
            'tempat'            => 'Aula',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['notulis_username']);
    }
}

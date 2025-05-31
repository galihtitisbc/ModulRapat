<?php
namespace Modules\Rapat\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Rapat\Http\Helper\RoleGroupHelper;
use Modules\Rapat\Http\Helper\StatusPesertaRapat;
use Modules\Rapat\Http\Helper\StatusTindakLanjut;

class RapatAgenda extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function newFactory()
    {
        return \Modules\Rapat\Database\factories\RapatAgendaFactory::new ();
    }
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($rapatAgenda) {
            $rapatAgenda->slug = static::generateUniqueSlug($rapatAgenda->agenda_rapat);
        });

        static::updating(function ($rapatAgenda) {
            if ($rapatAgenda->isDirty('waktu_mulai') || $rapatAgenda->isDirty('waktu_selesai')) {
                foreach ($rapatAgenda->rapatAgendaPeserta as $value) {
                    $rapatAgenda->rapatAgendaPeserta()->updateExistingPivot(
                        $value->username, ['status' => StatusPesertaRapat::MENUNGGU->value]
                    );
                }
            }
            if ($rapatAgenda->isDirty('agenda_rapat')) {
                $rapatAgenda->slug = static::generateUniqueSlug($rapatAgenda->agenda_rapat, $rapatAgenda->id);
            }
        });
    }
    public function scopePegawaiIsPesertaOrCreator($query, $username)
    {
        // Jika BUKAN di halaman dashboard dan pegawai adalah pimpinan, maka return query tanpa filter
        if (! request()->is('rapat/dashboard') &&
            RoleGroupHelper::userHasRoleGroup(Auth::user(), RoleGroupHelper::pimpinanRoles())) {
            return $query;
        }

        $query->whereHas('rapatAgendaPeserta', function ($q) use ($username) {
            $q->where('pegawai_username', $username);
        })
            ->orWhere('pegawai_username', $username);
        return $query;
    }
    // public function scopeShowTindakLanjut($query, $userId)
    // {
    //     $query->whereHas('rapatTindakLanjut', function ($q) use ($userId) {
    //         $q->where('user_id', $userId);
    //     })
    //         ->orWhere('pimpinan_id', $userId)
    //         ->orWhere(function ($q) use ($userId) {
    //             $q->where('user_id', $userId)
    //                 ->whereHas('rapatTindakLanjut');
    //         });

    //     return $query;
    // }
    public function getStatusTindakLanjutAttribute()
    {
        if ($this->rapatTindakLanjut->contains('status', StatusTindakLanjut::BELUM_SELESAI->value)) {
            return StatusTindakLanjut::BELUM_SELESAI->value;
        }
        return StatusTindakLanjut::SELESAI->value;
    }
    public function getStatusPersentasePenyelesaianAttribute()
    {
        $jmlhTindakLanjut = $this->rapatTindakLanjut->count();
        $jmlSelesai       = $this->rapatTindakLanjut->where('status', StatusTindakLanjut::SELESAI->value)->count();
        return round(($jmlSelesai / $jmlhTindakLanjut) * 100);

    }
    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }
    public function rapatAgendaPimpinan()
    {
        return $this->belongsTo(Pegawai::class, 'pimpinan_username', 'username', 'id');
    }
    public function rapatAgendaNotulis()
    {
        return $this->belongsTo(Pegawai::class, 'notulis_username', 'username', 'id');
    }
    public function rapatAgendaPeserta()
    {
        return $this->belongsToMany(Pegawai::class, 'rapat_pesertas', 'rapat_agenda_id', 'pegawai_username', 'id', 'username')->withPivot('status', 'is_penugasan', 'link_konfirmasi');
    }
    public function rapatLampiran()
    {
        return $this->hasMany(RapatLampiran::class, 'rapat_agenda_id');
    }
    public function rapatDokumentasi()
    {
        return $this->hasMany(RapatDokumentasi::class, 'rapat_agenda_id');
    }
    public function rapatTindakLanjut()
    {
        return $this->hasMany(RapatTindakLanjut::class, 'rapat_agenda_id');
    }
    public function rapatKepanitiaan()
    {
        return $this->belongsTo(Kepanitiaan::class, 'kepanitiaan_id');
    }
    public function rapatNotulen()
    {
        return $this->hasOne(RapatNotulen::class, 'rapat_agenda_id');
    }
    private static function generateUniqueSlug($judul, $ignoreId = null)
    {
        $slug         = Str::slug($judul);
        $originalSlug = $slug;
        $count        = 1;
        while (static::where('slug', $slug)
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
}

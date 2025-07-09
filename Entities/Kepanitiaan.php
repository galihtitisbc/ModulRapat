<?php
namespace Modules\Rapat\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Rapat\Http\Helper\RoleGroupHelper;

class Kepanitiaan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts   = [
        'struktur' => 'array',
    ];
    // protected static function booted()
    // {
    //     static::addGlobalScope(new PegawaiAnggotaKepanitiaan);
    // }
    public function getRouteKeyName()
    {
        return 'slug';
    }
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($kepanitiaan) {
            $kepanitiaan->slug = static::generateUniqueSlug($kepanitiaan->nama_kepanitiaan);
        });

        static::updating(function ($kepanitiaan) {
            if ($kepanitiaan->isDirty('nama_kepanitiaan')) {
                $kepanitiaan->slug = static::generateUniqueSlug($kepanitiaan->nama_kepanitiaan, $kepanitiaan->id);
            }
        });
    }
    public function scopePegawaiIsAnggotaPanitia($query, $id)
    {
        if (RoleGroupHelper::userHasRoleGroup(Auth::user(), RoleGroupHelper::pimpinanRoles()) || RoleGroupHelper::userHasRoleGroup(Auth::user(), RoleGroupHelper::kepegawaianRoles())) {
            return $query;
        }
        $query->whereHas('pegawai', function ($q) use ($id) {
            $q->where('id', $id);
        });
        return $query;
    }
    public function pegawai()
    {
        return $this->belongsToMany(Pegawai::class, 'kepanitiaan_pegawai', 'kepanitiaan_id', 'pegawai_id', 'id', 'id');
    }
    public function rapatAgenda()
    {
        return $this->hasMany(RapatAgenda::class, 'kepanitiaan_id');
    }
    public function ketua()
    {
        return $this->belongsTo(Pegawai::class, 'pimpinan_id', 'id');
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

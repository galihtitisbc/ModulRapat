<?php
namespace Modules\Rapat\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Kepanitiaan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // protected static function newFactory()
    // {
    //     return \Modules\Rapat\Database\factories\KepanitiaanFactory::new();
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
    public function scopePegawaiIsAnggotaPanitia($query, $username)
    {
        $query->whereHas('pegawai', function ($q) use ($username) {
            $q->where('username', $username);
        });
        return $query;
    }
    public function pegawai()
    {
        return $this->belongsToMany(Pegawai::class, 'kepanitiaan_pegawai', 'kepanitiaan_id', 'pegawai_username', 'id', 'username');
    }
    public function rapatAgenda()
    {
        return $this->hasMany(RapatAgenda::class, 'kepanitiaan_id');
    }
    public function ketua()
    {
        return $this->belongsTo(Pegawai::class, 'pimpinan_username', 'username');
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

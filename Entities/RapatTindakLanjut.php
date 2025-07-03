<?php
namespace Modules\Rapat\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RapatTindakLanjut extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // protected static function newFactory()
    // {
    //     return \Modules\Rapat\Database\factories\RapatTindakLanjutFactory::new();
    // }
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($rapatTindakLanjut) {
            $rapatTindakLanjut->slug = static::generateUniqueSlug($rapatTindakLanjut->deskripsi_tugas);
        });

        static::updating(function ($rapatTindakLanjut) {
            if ($rapatTindakLanjut->isDirty('deskripsi_tugas')) {
                $rapatTindakLanjut->slug = static::generateUniqueSlug($rapatTindakLanjut->deskripsi_tugas, $rapatTindakLanjut->id);
            }
        });
    }

    public function scopePegawaiHaveTugas($query, $pegawai, $rapatAgenda)
    {
        //untuk menampilkan daftar tugas pada agenda rapat tertentu
        $query->when($rapatAgenda->pimpinan_id == $pegawai->id || $rapatAgenda->notulis_id == $pegawai->id, function ($q) use ($rapatAgenda) {
            $q->where('rapat_agenda_id', $rapatAgenda->id);
        }, function ($q) use ($pegawai) {
            $q->where('pegawai_id', $pegawai->id);
        });
        return $query;
    }

    public function scopeListAgendaRapatHaveTugas($query, $id)
    {
        $query->whereHas('rapatAgenda', function ($q) use ($id) {
            $q->where('pimpinan_id', $id)
                ->orWhere('notulis_id', $id);
        })
            ->orWhere('pegawai_id', $id);
        return $query;
    }
    public function rapatAgenda()
    {
        return $this->belongsTo(RapatAgenda::class, 'rapat_agenda_id');
    }
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id', 'id', 'id');
    }
    public function rapatTindakLanjutFile()
    {
        return $this->hasMany(RapatTindakLanjutFile::class, 'rapat_tindak_lanjut_id');
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
